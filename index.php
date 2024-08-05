<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Adding Mass Reviews of WooCommerce Products</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/js-cookie/3.0.1/js.cookie.min.js"></script>
        <style>
            .product-image {
                display: block;
                margin-left: auto;
                margin-right: auto;
                width: 100%;
                max-width: 400px;
                max-height: 400px;
                object-fit: cover;
            }
            footer {
                text-align: center;
                margin-top: 30px;
                padding: 10px;
                background-color: #f8f9fa;
            }
            h1 {
                text-align:  center;
            }
        </style>
    </head>
    <body>
    <div class="container">
        <h1 class="mt-4">Adding Mass Reviews of WooCommerce Products</h1>
        <div class="row">
            <div class="col-md-6">
                <form id="reviewForm" class="mt-4">
                    <h2>Connection Details</h2>
                    <div class="mb-3">
                        <label for="consumer_key" class="form-label">Consumer Key</label>
                        <input type="text" class="form-control" id="consumer_key" required>
                    </div>
                    <div class="mb-3">
                        <label for="private_key" class="form-label">Secret Key</label>
                        <input type="text" class="form-control" id="private_key" required>
                    </div>
                    <div class="mb-3">
                        <label for="wp_url" class="form-label">WordPress URL</label>
                        <input type="url" class="form-control" id="wp_url" placeholder="https://example.com" required>
                    </div>

                    <h2>Review Details</h2>
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="product_id" class="form-label">Product ID</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="product_id" required>
                            <button class="btn btn-outline-secondary" type="button" id="viewDetails">View Details</button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="review_content" class="form-label">Review Content</label>
                        <textarea class="form-control" id="review_content" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="rating" class="form-label">Rating (1-5)</label>
                        <input type="number" class="form-control" id="rating" min="1" max="5" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
                <div id="alertContainer" class="mt-3"></div>
            </div>
            <div class="col-md-6">
                <div id="productSummary" class="text-center mt-4"></div>
            </div>
        </div>
        <div class="mt-5">
            <h2>About This Page</h2>
            <p>
                <strong>How does this page work?</strong><br>
                This page allows you to efficiently add multiple reviews to products in your WooCommerce store. Just fill in the connection and review fields and then add a review to the product with one click.
            </p>
            <p>
                <strong>What data do we store?</strong><br>
                We store your client key, secret key and WordPress URL <u>in cookies on your browser</u> to make it easier for you to use the site next time. The API key is safely stored in your browser's cookies and <u>not on our servers</u>, making it very secure. You can always delete cookies using the link below.<br>
                <a href="#" id="clearCookies">Click here to clear stored cookies</a>
            </p>
            <p>
                <strong>Is it safe?</strong><br>
                Yes, storing keys in cookies is a secure way to save your credentials temporarily. Ensure that your keys are kept private.
            </p>
            <p>
                <strong>How many reviews can I add?</strong><br>
                You can add <u>as many reviews as needed</u>u>, but be mindful of WooCommerce's rate limits and review policies.
            </p>
            <p>
                <strong>How does View Details work?</strong><br>
                Enter the product ID to download basic data about the product: name, photo and description.
            </p>
            <p>
                <strong>Is this application paid?</strong><br>
                No, this <u>application is free</u>. You can support the author via the <a href="https://buymeacoffee.com/wudoka">Buy Me A Coffee</a> platform.
            </p>
        </div>
    </div>

    <footer>
        <p>example.com - All Rights Reserved - 2024 - Crafted by WUDOKA</p>
    </footer>

    <script>
    $(document).ready(function() {
        if (Cookies.get('consumer_key')) {
            $('#consumer_key').val(Cookies.get('consumer_key'));
        }
        if (Cookies.get('private_key')) {
            $('#private_key').val(Cookies.get('private_key'));
        }
        if (Cookies.get('wp_url')) {
            $('#wp_url').val(Cookies.get('wp_url'));
        }

        $('#viewDetails').click(function() {
            const $button = $(this);
            $button.prop('disabled', true).text('Please Wait');

            const productId = $('#product_id').val();
            const wpUrl = $('#wp_url').val();
            const consumerKey = $('#consumer_key').val();
            const privateKey = $('#private_key').val();

            $.ajax({
                url: 'proxy.php',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    wp_url: wpUrl,
                    consumer_key: consumerKey,
                    consumer_secret: privateKey,
                    endpoint: '/wp-json/wc/v3/products/' + productId,
                    method: 'GET'
                }),
                success: function(product) {
                    const summary = `
                        <h3>${product.name}</h3>
                        <img src="${product.images.length ? product.images[0].src : ''}" alt="${product.name}" class="product-image">
                        <p>${product.description.replace(/(\r\n|\n|\r)/gm, "").substring(0, 1000)}</p>
                    `;
                    $('#productSummary').html(summary);
                    $button.prop('disabled', false).text('View Details');
                },
                error: function(xhr) {
                    console.error(xhr);
                    $('#productSummary').html('<p class="text-danger">Brak produktu o tym ID.</p>');
                    $button.prop('disabled', false).text('View Details');
                }
            });
        });

        $('#reviewForm').submit(function(event) {
            event.preventDefault();
            const wpUrl = $('#wp_url').val();
            const consumerKey = $('#consumer_key').val();
            const privateKey = $('#private_key').val();
            const productId = $('#product_id').val();
            const data = {
                product_id: productId,
                review: $('#review_content').val(),
                reviewer: $('#name').val(),
                reviewer_email: 'example@example.com',
                rating: $('#rating').val()
            };

            Cookies.set('consumer_key', consumerKey, { expires: 7 });
            Cookies.set('private_key', privateKey, { expires: 7 });
            Cookies.set('wp_url', wpUrl, { expires: 7 });

            $.ajax({
                url: 'proxy.php',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    wp_url: wpUrl,
                    consumer_key: consumerKey,
                    consumer_secret: privateKey,
                    endpoint: '/wp-json/wc/v3/products/reviews',
                    data: data
                }),
                success: function(response) {
                    $('#alertContainer').html('<div class="alert alert-success" role="alert">Review added successfully!</div>');
                    $('#name').val('');
                    $('#review_content').val('');
                    $('#rating').val('');
                    $('#productSummary').html('');
                },
                error: function(xhr) {
                    console.error(xhr);
                    $('#alertContainer').html('<div class="alert alert-danger" role="alert">Failed to add review. ' + (xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : '') + '</div>');
                }
            });
        });

        $('#clearCookies').click(function() {
            Cookies.remove('consumer_key');
            Cookies.remove('private_key');
            Cookies.remove('wp_url');
            location.reload();
        });
    });
    </script>
    </body>
</html>
