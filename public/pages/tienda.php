<main class="container py-5">
    <h1 class="h3 mb-4">Tienda</h1>
    <div class="row g-4">
        <?php foreach (['Jerseys', 'Hoodies', 'Gorras', 'Accesorios'] as $product) : ?>
            <div class="col-md-3">
                <div class="product-card">
                    <div class="product-thumb"></div>
                    <h2 class="h6 fw-bold mt-3"><?php echo $product; ?></h2>
                    <p class="small text-secondary">Productos oficiales LeagueDan.</p>
                    <button class="btn btn-outline-dark btn-sm" type="button">Agregar</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>
