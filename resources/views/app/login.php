<?php $v->layout("theme"); ?>

<!-- ============================================-->
<!-- <section> begin ============================-->
<section class="">
    <div class="container">
        <div class="container ">
            <form action="<?= url("/login"); ?>"  method="post" >
                <?= csrf_input(); ?>
                <div class="form-group">
                    <div class="col-md-6 offset-md-3 ajax-response">
                        <?= flash(); ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-6 offset-md-3">
                        <label for="email">Email:</label>
                        <input id="email" type="text" name="email" class="form-control " placeholder="Seu email" required="" >
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-6 offset-md-3">
                        <label for="password">Senha:</label>
                        <input id="password" type="password" name="password" class="form-control" placeholder="Sua senha" required="" >
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-6 offset-md-3">
                        <input type="submit" value="Login" class="btn btn-primary">
                    </div>
                </div>
            </form>
        </div>
</section>

<!-- <section> close ============================-->
<!-- ============================================-->




