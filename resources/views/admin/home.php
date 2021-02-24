<?php $v->layout("theme"); ?>

<section>
    <div class="container">
        <div class="row d-flex justify-content-center align-self-center">
            <div>
                <div>
                    <?= flash(); ?>
                </div>
                <h1>Configure sua aplicação do mercado livre:</h1>
                <p>
                    O mercado livre precisa autorizar a sua aplicação para utilizar a sua api, lembre-se de ter configurado as informações necessarias no arquivo .env
                </p>
                <a href="<?= $link; ?>" class="btn btn-outline-primary">Clique aqui para autorizar</a>
            </div>
        </div>
    </div>
</section>


