// Password Generator
document.getElementById('generate-password').addEventListener('click', function() {
    document.getElementById('password').value = Math.floor(10000 + Math.random() * 90000); // Generate 5-digit password
});

document.addEventListener("DOMContentLoaded", function() {
    const stateSelect = document.getElementById("state");
    const citySelect = document.getElementById("city");
    const customAddressToggle = document.getElementById("custom_address_toggle");
    const customAddressInput = document.getElementById("custom_address");

    // Populate states
    Object.keys(citiesByState).forEach(state => {
        const option = document.createElement("option");
        option.value = state;
        option.textContent = state;
        stateSelect.appendChild(option);
    });

    // Handle state change
    stateSelect.addEventListener("change", function() {
        const selectedState = this.value;
        citySelect.innerHTML = '<option value="">-- Select City --</option>'; // Reset city dropdown

        if (selectedState && citiesByState[selectedState]) {
            citiesByState[selectedState].forEach(city => {
                const option = document.createElement("option");
                option.value = city;
                option.textContent = city;
                citySelect.appendChild(option);
            });
        }
    });

    // Handle custom address toggle
    customAddressToggle.addEventListener("change", function() {
        if (this.checked) {
            customAddressInput.disabled = false;
            customAddressInput.required = true;
        } else {
            customAddressInput.disabled = true;
            customAddressInput.required = false;
            customAddressInput.value = ''; // Reset field when unchecked
        }
    });
});

document.getElementById('course_id').addEventListener('change', function() {
    const selectedCourse = this.value;
    const batchContainer = document.getElementById('batch-container');
    const paymentSection = document.getElementById('payment-section');
    const courseOption = this.options[this.selectedIndex];
    const bookingAmountInput = document.getElementById('booking_amount');
    const remainingFeeInput = document.getElementById('remaining_fee');
    const installment1Input = document.getElementById('installment_1');
    const installment2Input = document.getElementById('installment_2');

    batchContainer.style.display = 'none';
    paymentSection.style.display = 'none';

    if (selectedCourse) {
        // Fetch course details (booking amount)
        fetch(`get_course_details.php?course_id=${selectedCourse}`)
            .then(response => response.json())
            .then(course => {
                if (course) {
                    const totalFee = parseFloat(course.fee);
                    const bookingAmount = parseFloat(course.booking_amount);

                    bookingAmountInput.value = bookingAmount;
                    remainingFeeInput.value = totalFee - bookingAmount;

                    // Calculate installment amounts
                    const remainingFee = totalFee - bookingAmount;
                    const installmentAmount = remainingFee / 2;
                    installment1Input.value = installmentAmount;
                    installment2Input.value = installmentAmount;

                    // Show payment section
                    paymentSection.style.display = 'block';
                }
            })
            .catch(error => console.error('Error fetching course details:', error));

        // Fetch batches based on the selected course
        fetch(`get_batches.php?course_id=${selectedCourse}`)
            .then(response => response.json())
            .then(batches => {
                const batchSelect = document.getElementById('batch_id');
                batchSelect.innerHTML = '<option value="">-- Select Batch --</option>'; // Reset dropdown

                batches.forEach(batch => {
                    const option = document.createElement('option');
                    option.value = batch.batch_id;
                    option.textContent = batch.batch_name;
                    batchSelect.appendChild(option);
                });

                // Show batch selection
                batchContainer.style.display = 'block';
            })
            .catch(error => console.error('Error fetching batches:', error));
    }
});

// Handle Payment Option Selection
document.querySelectorAll('input[name="payment_option"]').forEach(option => {
    option.addEventListener('change', function() {
        const bookingAmount = parseFloat(document.getElementById('booking_amount').value) || 0;
        const remainingFeeInput = document.getElementById('remaining_fee');
        const installment1Input = document.getElementById('installment_1');
        const installment2Input = document.getElementById('installment_2');
        const courseOption = document.getElementById('course_id').options[document.getElementById('course_id').selectedIndex];
        const courseFee = parseFloat(courseOption.getAttribute('data-fee')) || 0;

        // Calculate the correct remaining fee
        const remainingFee = courseFee - bookingAmount;

        if (this.value === "no_payment") {
            installment1Input.value = (remainingFee / 2).toFixed(2);
            installment2Input.value = (remainingFee / 2).toFixed(2);
            remainingFeeInput.value = remainingFee.toFixed(2);
        } else if (this.value === "first_installment") {
            installment1Input.value = (remainingFee / 2).toFixed(2);
            installment2Input.value = 0;
            remainingFeeInput.value = installment1Input.value;
        } else if (this.value === "full_payment") {
            installment1Input.value = remainingFee.toFixed(2);
            installment2Input.value = 0;
            remainingFeeInput.value = 0;
        }
    });
});