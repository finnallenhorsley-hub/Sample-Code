<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

do_action( 'woocommerce_before_checkout_form', $checkout );

if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
    echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
    return;
}
?>
<div class="multi-step-bar-main">
    <div class="custom-container">
          <div class="multi-step-bar-cont">
              <div class="row">
                  <div class="col-md-4">
                      <div class="step-bar-col active">
                          <h2>Create Account</h2>
                      </div>
                  </div>
                  <div class="col-md-4">
                      <div class="step-bar-col">
                          <h2>Student Info</h2>
                      </div>
                  </div>
                  <div class="col-md-4">
                      <div class="step-bar-col">
                          <h2>Payment</h2>
                      </div>
                  </div>
              </div>
          </div>
    </div>
</div>
<div class="multi-step-title-cont">
    <div class="custom-container">
         <div class="multi-step-title-col">
             <h3 class="multi-step-title">Create An Account</h3>
         </div>
    </div>
</div>
<div id="multi-step-checkout">
<div class="custom-container">
    <div class="row">
        <div class="col-md-8">
                <p class="cart-text">Item</p>
                <ul class="cart-items-list">
                    <?php
                    $cart = WC()->cart->get_cart();
                    if (!empty($cart)) {
                        foreach ($cart as $cart_item_key => $cart_item) {
                            $product = $cart_item['data'];
                            $product_id = $product->get_id();
                            $product_name = $product->get_name();
                            $product_price = wc_price($product->get_price());
                            $product_quantity = $cart_item['quantity'];
                            $product_permalink = get_permalink($product_id);
                            $product_thumbnail = $product->get_image();
                            $attributes = $product->get_attributes(); // Get attributes
                            ?>
                            <li class="cart-item">
                                <a href="<?php echo esc_url($product_permalink); ?>">
                                    <strong><?php echo esc_html($product_name); ?></strong>
                                </a>
                                <p><?php echo sprintf(esc_html__('Qty: %s', 'woocommerce'), $product_quantity); ?></p>
                                <p><?php echo sprintf(esc_html__('Price: %s', 'woocommerce'), $product_price); ?></p>
            
                                <!-- Display Product Attributes -->
                                <?php if (!empty($attributes)) : ?>
                                    <ul class="product-attributes">
                                        <?php foreach ($attributes as $attribute_name => $attribute) : ?>
                                            <?php
                                            $attribute_label = wc_attribute_label($attribute_name);
                                            $attribute_value = $product->get_attribute($attribute_name);
                                            ?>
                                            <li><strong><?php echo esc_html(str_replace('-', ' ', $attribute_label)); ?>:</strong> <?php echo esc_html($attribute_value); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </li>
                            <?php
                        }
                        ?>
                    </ul>
            
                    <!-- Subtotal -->
                    <p class="cart-total">
                        <span><?php esc_html_e('Subtotal:', 'woocommerce'); ?></span>
                        <span>$<?php echo WC()->cart->get_subtotal(); ?></span>
                    </p>
            
                    <!-- Discount -->
                    <p class="cart-total">
                        <span><?php esc_html_e('Discount:', 'woocommerce'); ?></span>
                        <span style="color: #008000;">
                            <?php
                            $discount_total = WC()->cart->get_discount_total();
                            echo ($discount_total > 0) ? '-' . wc_price($discount_total) : wc_price(0);
                            ?>
                        </span>
                    </p>
            
                    <!-- Total Paid Today -->
                    <p class="cart-total">
                        <span><?php esc_html_e('TOTAL PAID TODAY:', 'woocommerce'); ?></span>
                        <span style="color: #EE2C3C;"><?php echo WC()->cart->get_total(); ?></span>
                    </p>
            
                    <?php
                    } else {
                        echo '<p>' . esc_html__('Your cart is empty.', 'woocommerce') . '</p>';
                    }
                    ?>
            </div>

        <div class="col-md-4">
            <div class="checkout-form-col-cont">
                <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

                    <!-- Step 1: Parent Info -->
                    <div class="checkout-step create-account-col" id="step-1">
                        <h3>Tell us about yourself</h3>
                        <div class="step-content">
                            <p class="form-row">
                                <input type="text" name="parent_first_name" id="parent_first_name" required placeholder="Parent First Name">
                            </p>
                            <p class="form-row">
                                <input type="text" name="parent_last_name" id="parent_last_name" required placeholder="Parent Last Name">
                            </p>
                            <p class="form-row">
                                <input type="email" name="parent_email" id="parent_email" required placeholder="Parent Email Address">
                            </p>
                            <p class="form-row">
                                <input type="password" name="password" id="current_password" required placeholder="Password">
                            </p>
                            <p class="form-row">
                                <input type="password" name="confirm_password" id="confirm_password" required placeholder="Confirm Password">
                                <span id="password-error" style="color: red; display: none;">Passwords do not match!</span>
                            </p>
                            <p class="form-row">
                                <input type="tel" name="parent_phone" id="parent_phone" required placeholder="Phone Number">
                            </p>
                            <p class="form-row">
                                <a href="/my-account/"><?php esc_html_e( 'Already have an account? Sign in', 'woocommerce' ); ?></a>
                            </p>
                        </div>
                        <button type="button" class="button next-step">Create My Account</button>
                    </div>
                    
                    <!-- Step 2: Student Info -->
                    <div class="checkout-step" id="step-2" style="display: none;">
                        <h3>Student Info</h3>
                        <p>Please let us know about the student who will be studying with us and where to ship class materials.</p>
                        <div class="step-content">
                            <p class="form-row">
                                <input type="text" name="student_first_name" id="student_first_name" required placeholder="Student First Name">
                            </p>
                            <p class="form-row">
                                <input type="text" name="student_last_name" id="student_last_name" required placeholder="Student Last Name">
                            </p>
                            <p class="form-row">
                                <input type="email" name="student_email" id="student_email" required placeholder="Student Email">
                            </p>
                            <p class="form-row">
                                <input type="tel" name="student_phone" id="student_phone" required placeholder="Student Phone Number">
                            </p>
                            <p class="form-row">
                                <input type="text" name="country" id="country" required placeholder="Country">
                            </p>
                        </div>
                        <button type="button" class="button prev-step">Back</button>
                        <button type="button" class="button next-step">Next</button>
                    </div>
                    
                    <!-- Step 3: Payment Info -->
                    <div class="checkout-step" id="step-3" style="display: none;">
                        <h3>Enter Your Payment Information</h3>
                        <div class="step-content">
                            <?php do_action( 'woocommerce_checkout_order_review' ); ?>
                        </div>
                        <button type="button" class="button prev-step">Back</button>
                        <button type="submit" class="button custom-toggle-btn"><?php esc_html_e( 'Place Order', 'woocommerce' ); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

   
   
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let currentStep = 1;
    const steps = document.querySelectorAll('.checkout-step');
    const nextButtons = document.querySelectorAll('.next-step');
    const prevButtons = document.querySelectorAll('.prev-step');
    const stepTitles = ["Create An Account", "Student Info", "Payment"];
    const multiStepTitle = document.querySelector('.multi-step-title');
    const stepBars = document.querySelectorAll('.step-bar-col');

    function updateStepBar(step) {
        stepBars.forEach((bar, index) => {
            bar.classList.toggle('active', index === step - 1);
        });
        multiStepTitle.textContent = stepTitles[step - 1];
    }

    function validateStep(step) {
        const inputs = steps[step - 1].querySelectorAll('input[required]');
        let isValid = true;

        inputs.forEach(input => {
            if (!input.value.trim()) {
                input.classList.add('error');
                isValid = false;
            } else {
                input.classList.remove('error');
            }
        });

        // Password validation on Step 1
        if (step == 1) {
            const password = document.getElementById('current_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const passwordError = document.getElementById('password-error');

            if (password !== confirmPassword) {
                passwordError.style.display = 'block';
                return false;
            } else {
                passwordError.style.display = 'none';
            }
        }

        return isValid;
    }

    nextButtons.forEach(button => {
        button.addEventListener('click', function () {
            if (!validateStep(currentStep)) {
                alert("Please fill all required fields correctly before proceeding.");
                return;
            }

            // Check if email exists before moving to the next step
            if (currentStep === 1) {
                const email = document.getElementById('parent_email').value;
                fetch('/check_email.php?email=' + encodeURIComponent(email))
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            alert("This email is already registered. Please use another email.");
                        } else {
                            moveToNextStep();
                        }
                    });
            } else {
                moveToNextStep();
            }
        });
    });

    function moveToNextStep() {
        steps[currentStep - 1].style.display = 'none';
        currentStep++;
        steps[currentStep - 1].style.display = 'block';
        updateStepBar(currentStep);
    }

    prevButtons.forEach(button => {
        button.addEventListener('click', function () {
            steps[currentStep - 1].style.display = 'none';
            currentStep--;
            steps[currentStep - 1].style.display = 'block';
            updateStepBar(currentStep);
        });
    });

    updateStepBar(currentStep);
});
document.addEventListener("DOMContentLoaded", function () {
    const paymentRadios = document.getElementsByName("payment_method");
    const placeOrderButton = document.querySelector(".custom-toggle-btn");
    const paypalButton = document.getElementById("ppc-button-ppcp-gateway");

    function togglePlaceOrderButton() {
        // Check if the PayPal button has display: none
        const isPayPalHidden = window.getComputedStyle(paypalButton).display === "none";

        if (isPayPalHidden) {
            placeOrderButton.style.display = "none";
        } else {
            placeOrderButton.style.display = "block";
        }
    }

    // Run on page load
    togglePlaceOrderButton();

    // Listen for changes on payment method selection
    paymentRadios.forEach(radio => {
        radio.addEventListener("change", togglePlaceOrderButton);
    });

    // Observe any style changes in the PayPal button
    const observer = new MutationObserver(togglePlaceOrderButton);
    observer.observe(paypalButton, { attributes: true, attributeFilter: ["style"] });
});
</script>

