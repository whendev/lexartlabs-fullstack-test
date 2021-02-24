
<div class="card shadow-sm border-0  mb-5 p-2">
    <div class="container">
        <div class="row g-0">
            <div class="col-3 d-flex justify-content-center ">
                <img class="img-testimonial" src="<?= $product->thumbnail ?>" alt="" style="max-width: 118px;max-height: 118px">
            </div>
            <div class="col-9">
                <div class="d-inline ">
                    <p><?= $product->title; ?></p>
                    <p>R$  <?= number_format($product->price, 2); ?></p>
                    <a class="btn btn-primary" href="<?= $product->permalink; ?>" target="_blank">Ver anuncio</a>
                </div>
            </div>
        </div>
    </div>
</div>
