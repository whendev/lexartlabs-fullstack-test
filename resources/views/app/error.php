<?php $v->layout("theme"); ?>
<!-- ============================================-->
<!-- <section> begin ============================-->
<section>
    <div class="container">
        <div class="row ">
            <div class="col">
                <div class=" text-center">
                    <h2 class="my-3">Oops, não foi possível processar sua solicitação :(</h2>
                    <h3 class="my-3">CODIGO DO ERRO: <b><?= $errCode; ?></b></h3>
                    <div class="text-center">
                        <a class="btn btn-primary" href="<?= url(); ?>">Voltar para pagina inicial</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- <section> close ============================-->
<!-- ============================================-->



