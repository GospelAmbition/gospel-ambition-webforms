<?php



function go_display_opt_in( $atts ){

    $source = $atts['source'] ?? null;

    ob_start();
    ?>
    <style>
        .dt-form-error {
            color: #cc4b37;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .dt-form-success {
            color: #4CAF50;
            font-size: 0.8rem;
            font-weight: bold;
        }
    </style>
    <div class="go-opt-in__form">
        <form id="go-optin-form" action="/wp-json/go-webform/optin" method="post">
            <label>
               <strong>Name (optional)</strong>
            </label>
            <div class="input-group">
                <input type="text" name="first_name" placeholder="First" class="input-group-field">
                <input type="text" name="last_name" placeholder="Last" class="input-group-field">
            </div>
            <label>
                <strong>Email Address</strong>
            </label>
            <div class="input-group">
                <input type="email" name="email2" placeholder="Email Address" class="input-group-field">
                <input type="email" name="email" placeholder="Email Address" class="input-group-field" style="display: none">
                <div class="input-group-button">
                    <button id='go-submit-form-button' type="submit" class="button">
                        Subscribe
                        <img id="go-submit-spinner" style="display: none; height: 25px" src="<?php echo esc_html( GO_Webform_Context_Switcher::plugin_url( '/assets/spinner-white.svg' ) ) ?>"/>
                    </button>
                </div>
            </div>
            <div class="dt-form-success"></div>
            <span class="dt-form-error"></span>
        </form>
    </div>
    <script>
        let go_form = document.getElementById('go-optin-form');
        let error_span = go_form.querySelector('.dt-form-error');
        go_form.addEventListener('submit', function(e){
            e.preventDefault();
            go_form.querySelector('#go-submit-spinner').style.display = 'inline-block';
            go_form.querySelector('#go-submit-form-button').disabled = true;
            let email = go_form.querySelector('input[name="email"]').value;
            let email2 = go_form.querySelector('input[name="email2"]').value;
            if ( email ){
                return
            }
            let data = {
              email: email2,
              first_name: go_form.querySelector('input[name="first_name"]').value,
              last_name: go_form.querySelector('input[name="last_name"]').value,
              source: '<?php echo $source ?>'
            }

            fetch('/wp-json/go-webform/optin', {
                method: 'POST',
                body: JSON.stringify(data),
                headers: {
                    'Content-Type': 'application/json'
                }
            }).then(function(response){
                go_form.querySelector('#go-submit-spinner').style.display = 'none';
                go_form.querySelector('#go-submit-form-button').disabled = false;
                if ( response.status !== 200 ){
                    error_span.innerHTML = 'There was an error subscribing you. Please try again.';
                    error_span.style.display = 'block';
                } else {
                    error_span.style.display = 'none';
                    go_form.querySelector('.dt-form-success').innerHTML = 'You have been subscribed!';
                    go_form.querySelector('input[name="email2"]').value = '';
                }
            }).catch(function(error){
                go_form.querySelector('#go-submit-spinner').style.display = 'none';
                error_span.innerHTML = 'There was an error subscribing you. Please try again.';
                error_span.style.display = 'block';
            })
        });
    </script>

    <?php

    return ob_get_clean();
}
add_shortcode( 'go_display_opt_in', 'go_display_opt_in' );
