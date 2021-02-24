<?php


namespace Source\Core;


use PDOException;
use Source\support\Message;

/**
 * Class Model
 * @package Source\Core
 */
abstract class Model
{
    /**
     * @var object|null
     */
    protected ?object $data;
    /**
     * @var PDOException|null
     */
    protected $fail;

    /**
     * @var Message|null
     */
    protected ?Message $message;

    /**
     * @var null|string
     */
    protected ?string $query = "";
    /**
     * @var null|array
     */
    protected ?array $params = [];
    /**
     * @var null|string
     */
    protected ?string $order = "";

    /**
     * @var null|string
     */
    protected ?string $limit = "";

    /**
     * @var null|string
     */
    protected ?string $offset = "";

    /**
     * @var string
     */
    protected static string $entity;
    /**
     * @var array
     */
    protected static array $required;
    /**
     * @var array
     */
    protected static array $protected;

    /**
     * Model constructor.
     * @param string $entity
     * @param array $required
     * @param array $protected
     */
    public function __construct(string $entity, array $required, array $protected)
    {
        self::$entity = $entity;
        self::$required = $required;
        self::$protected = array_merge($protected, ["created_at", "updated_at"]);

        $this->message = new Message();
    }

    /**
     * @param $name
     * @return null
     */
    public function __get($name)
    {
        return ($this->data->$name ?? null);
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value): void
    {
        if (empty($this->data)){
            $this->data = new \stdClass();
        }

        $this->data->$name = $value;
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name): bool
    {
        return isset($this->data->$name);
    }

    /**
     * @return object|null
     */
    public function data(): ?object
    {
        return $this->data;
    }

    /**
     * @return PDOException|null
     */
    public function fail(): ?PDOException
    {
        return $this->fail;
    }

    /**
     * @return Message|null
     */
    public function message(): ?Message
    {
        return $this->message;
    }

    /**
     * @param string|null $terms
     * @param string|null $params
     * @param string $columns
     * @return $this
     */
    public function find(?string $terms = null, ?string $params = null, string $columns = "*"): Model
    {
        if ($terms){
            $this->query = "SELECT {$columns} FROM ". self::$entity ." WHERE {$terms}";
            parse_str($params, $this->params);
            return $this;
        }

        $this->query = "SELECT {$columns} FROM ".self::$entity;
        return $this;
    }

    /**
     * @param int $id
     * @param string $columns
     * @return null|mixed|Model
     */
    public function findById(int $id, string $columns = "*"): ?Model
    {
        $find = $this->find(" id = :id", "id={$id}", $columns);
        return $find->fetch();
    }

    /**
     * @param string $columnOrder
     * @return $this
     */
    public function order(string $columnOrder): Model
    {
        $this->order = " ORDER BY {$columnOrder}";
        return $this;
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function limit(int $limit): Model
    {
        $this->limit = " LIMIT {$limit}";
        return $this;
    }

    /**
     * @param int $offset
     * @return $this
     */
    public function offset(int $offset): Model
    {
        $this->offset = " OFFSET {$offset}";
        return $this;
    }

    /**
     * @param bool $all
     * @return array|mixed|null
     */
    public function fetch(bool $all = false)
    {
        try {
            $statement = Connect::getInstance()->prepare($this->query . $this->order . $this->limit . $this->offset);
            $statement->execute($this->params);

            if (!$statement->rowCount()){
                return null;
            }

            if ($all) {
                return $statement->fetchAll(\PDO::FETCH_CLASS, static::class);
            }

            return $statement->fetchObject(static::class);
        } catch (PDOException $exception){
            $this->fail = $exception;
            return null;
        }
    }

    /**
     * @return int
     */
    protected function count(): int
    {
        $statement = Connect::getInstance()->prepare($this->query);
        $statement->execute($this->params);
        return $statement->rowCount();
    }

    /**
     * @param array $data
     * @return int|null
     */
    protected function create(array $data): ?int
    {
        try {
            $columns = implode(", ", array_keys($data));
            $values = ":".implode(", :", array_keys($data));

            $statement = Connect::getInstance()->prepare("INSERT INTO ".self::$entity." ({$columns}) VALUES ({$values})");
            $statement->execute($this->filter($data));

            return Connect::getInstance()->lastInsertId();
        } catch (PDOException $exception){
            $this->fail = $exception;
            return null;
        }
    }


    /**
     * @param array $data
     * @param string $terms
     * @param string $params
     * @return int|null
     */
    protected function update(array $data, string $terms, string $params)
    {
        try {
            $dataSet = [];
            foreach ($data as $key => $value){
                $dataSet[] = "{$key} = :{$key}";
            }
            $dataSet = implode(", ", $dataSet);
            parse_str($params, $result);

            $statement = Connect::getInstance()->prepare("UPDATE ".self::$entity." SET {$dataSet} WHERE {$terms}");
            $statement->execute($this->filter(array_merge($data, $result)));

            return ($statement->rowCount() ?? 1);
        } catch (PDOException $exception){
            $this->fail = $exception;
            return null;
        }
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        if (!$this->required()){
            $this->message->warning('Preencha todos os campos para continuar');
            return false;
        }

        $id = $this->id;

        // UPDATE Access
        if (!empty($this->id)){
            $id = $this->id;
            $this->update($this->safe(), "id = :id", "id={$id}");
            if (!empty($this->fail)){
                $this->message->error("Erro ao atualizar, verifique os dados");
                return false;
            }
        }

        // CREATE Access
        if (empty($this->id)){
            $id = $this->create($this->safe());
            if (!empty($this->fail)){
                $this->message->error("Erro ao cadastrar, verifique os dados");
                return false;
            }
        }

        $this->data = $this->findById($id)->data();
        return true;
    }

    /**
     * @param string $terms
     * @param string|null $params
     * @return bool
     */
    public function delete(string $terms, string $params = null): bool
    {
        try {
            $statement = Connect::getInstance()->prepare("DELETE FROM ".self::$entity." WHERE {$terms}");
            if ($params){
                parse_str($params, $result);
                $statement->execute($result);
                return true;
            }
            $statement->execute();
            return true;
        } catch (PDOException $exception){
            $this->fail = $exception;
            return false;
        }
    }

    /**
     * @return bool
     */
    public function destroy(): bool
    {
        if (empty($this->id)){
            return false;
        }

        return $this->delete("id = :id", "id={$this->id}");
    }

    /**
     * @return array
     */
    protected function safe(): array
    {
        $safe = (array)$this->data();
        foreach (self::$protected as $key){
            unset($safe[$key]);
        }
        return $safe;
    }


    /**
     * @return bool
     */
    protected function required(): bool
    {
        $data = (array)$this->data();
        foreach (self::$required as $column){
            if (empty($data[$column])){
                return false;
            }
        }
        return true;
    }

    /**
     * @param array $data
     * @return array|null
     */
    private function filter(array $data): ?array
    {
        $filter = [];
        foreach ($data as $key => $value){
            $filter[$key] = (is_null($value) ? null : filter_var($value, FILTER_DEFAULT));
        }
        return $filter;
    }
}