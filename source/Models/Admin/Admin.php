<?php


namespace Source\Models\Admin;


use Source\Core\Model;

class Admin extends Model
{
    public function __construct()
    {
        parent::__construct("admin", ["email", "password"], ["id"]);
    }

    public function save(): bool
    {
        if (!is_passwd($this->password)){
            $min = CONF_PASSWD_MIN_LEN;
            $max = CONF_PASSWD_MAX_LEN;
            $this->message->warning("Sua senha deve ter entre {$min} a {$max} caracteres");
            return false;
        } else {
            $this->password = passwd($this->password);
        }

        // UPDATE
        if (!empty($this->id)){
            $userId = $this->id;
            if ($this->find("email = :email AND id != :id", "email={$this->email}&id={$userId}", "id")->fetch()){
                $this->message->error("O email informado já está cadastrado!");
                return false;
            }

            $this->update($this->safe(), "id = :id", "id={$userId}");
            if ($this->fail()){
                $this->message->error("Erro ao atualizar, verifique os dados e tente novamente. Caso o erro persista, contacte nosso suporte. ");
                return false;
            }
        }

        $userId = $this->id;

        // CREATE
        if (empty($this->id)){
            if ($this->findByEmail($this->email, "id")){
                $this->message->error("O email informado já está cadastrado!");
                return false;
            }

            $userId = $this->create($this->safe());
            if ($this->fail()){
                $this->message->error("Erro ao salvar, verifique os dados e tente novamente. Caso o erro persista, contacte nosso suporte. ");
                return false;
            }
        }

        $this->data = ($this->findById($userId))->data();
        return true;
    }

    public function findByEmail(string $email, string $columns = "*"): ?Admin
    {
        return $this->find("email = :email", "email={$email}", $columns)->fetch();
    }
}