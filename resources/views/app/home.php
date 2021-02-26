<?php $v->layout("theme"); ?>

<header class="py-5">
    <div class="container">
        <div class="row">
            <div class="col">
                <form action="<?= url('/search');?>" class="ajax_off" method="post" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group col-6 col-md-3">
                            <label class="w-100">
                                <select name="web" class="form-control bg-primary text-white">
                                    <option value="ml" <?= (empty($web) || $web == 'ml' ? "selected" : ""); ?>>Mercado Livre</option>
                                    <option value="bp" <?= (!empty($web) && $web == 'bp' ? "selected" : ""); ?>>Buscapé</option>
                                </select>
                            </label>
                        </div>

                        <div class="form-group col-6 col-md-3 ">
                            <label class="w-100 ">
                                <select name="category" class="form-control bg-primary text-white">
                                    <option value="" disabled <?= (empty($category) ? "selected" : ""); ?>>Categorias</option>
                                    <option value="geladeira" <?= (!empty($category) && $category == "geladeira" ? "selected" : ""); ?>>Geladeira</option>
                                    <option value="tv" <?= (!empty($category) && $category == "tv" ? "selected" : ""); ?>>TV</option>
                                    <option value="celular" <?= (!empty($category) && $category == "celular" ? "selected" : ""); ?>>Celular</option>
                                </select>
                            </label>
                        </div>

                        <div class="form-group col col-md-6">
                            <label class="w-100 d-flex">
                                <input class="form-control ml-md-3" type="text" name="s" placeholder="Search" aria-label="Search">
                                <button class="btn btn-primary ml-3" type="submit">Search</button>
                            </label>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row pt-5">
            <div class="col">
                <?= flash(); ?>
                <?php if ($products): ?>
                    <?php foreach ($products as $product): ?>
                        <?= $v->insert("products", ["product" => $product]); ?>
                    <?php endforeach; ?>
                <?= $paginator; ?>
                <div class="my-3">
                    <a class="btn btn-primary" href="<?= url(); ?>">Voltar pra home</a>
                </div>
                <?php elseif (isset($products) && count($products) == 0): ?>
                    <div class="alert alert-warning" role="alert">
                        Não encontramos nada sobre <b><?= $search; ?></b> no <b><?= ($web == "ml" ? "Mercado livre" : "Buscapé"); ?></b>, na categoria <b><?= $category; ?></b>
                    </div>
                <?php else: ?>
                    <h2>
                        Faça uma busca!
                    </h2>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>


