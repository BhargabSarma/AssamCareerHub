
    // Password Generator
    document.getElementById('generate-password').addEventListener('click', function() {
        document.getElementById('password').value = Math.floor(10000 + Math.random() * 90000); // Generate 5-digit password
    });

    document.addEventListener("DOMContentLoaded", function() {
        const stateSelect = document.getElementById("state");
        const citySelect = document.getElementById("city");

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
    })

    document.getElementById('course_id').addEventListener('change', function() {
        const selectedCourse = this.value;
        const batchContainer = document.getElementById('batch-container');
        const paymentSection = document.getElementById('payment-section');
        const courseOption = this.options[this.selectedIndex];
        const courseFee = parseFloat(courseOption.getAttribute('data-fee'));
        const bookingAmountInput = document.getElementById('booking_amount');
        const remainingFeeInput = document.getElementById('remaining_fee');
        const installment1Input = document.getElementById('installment_1');
        const installment2Input = document.getElementById('installment_2');

        batchContainer.style.display = 'none';
        paymentSection.style.display = 'none';

        if (selectedCourse) {
            // Show batches and payment section
            batchContainer.style.display = 'block';
            paymentSection.style.display = 'block';

            bookingAmountInput.value = 1000; // Assuming booking amount is 1000 by default
            remainingFeeInput.value = courseFee - 1000;

            // Calculate installment amounts
            const remainingFee = courseFee - 1000;
            const installmentAmount = remainingFee / 2;
            installment1Input.value = installmentAmount;
            installment2Input.value = installmentAmount;

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
                })
                .catch(error => console.error('Error fetching batches:', error));
        }
    });
