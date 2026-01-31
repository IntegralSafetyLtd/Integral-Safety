    </main>

    <footer class="bg-white border-t py-4 mt-8">
        <div class="max-w-7xl mx-auto px-4 text-center text-gray-500 text-sm">
            &copy; <?= date('Y') ?> <?= e(SITE_NAME) ?> - Admin Panel
        </div>
    </footer>

    <script>
        // Initialize Quill for rich text editors
        document.querySelectorAll('.quill-editor').forEach(function(container) {
            var hiddenInput = container.nextElementSibling;
            var quill = new Quill(container, {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'color': [] }, { 'background': [] }],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'indent': '-1'}, { 'indent': '+1' }],
                        [{ 'align': [] }],
                        ['link', 'image'],
                        ['clean']
                    ]
                }
            });

            // Set initial content
            if (hiddenInput && hiddenInput.value) {
                quill.root.innerHTML = hiddenInput.value;
            }

            // Update hidden field on text change
            quill.on('text-change', function() {
                if (hiddenInput) {
                    hiddenInput.value = quill.root.innerHTML;
                }
            });

            // Update hidden field before form submit
            var form = container.closest('form');
            if (form) {
                form.addEventListener('submit', function() {
                    if (hiddenInput) {
                        hiddenInput.value = quill.root.innerHTML;
                    }
                });
            }
        });

        // Confirm delete actions
        document.querySelectorAll('[data-confirm]').forEach(function(el) {
            el.addEventListener('click', function(e) {
                if (!confirm(this.dataset.confirm || 'Are you sure?')) {
                    e.preventDefault();
                }
            });
        });

        // SEO Character counters
        function setupCharCounter(inputId, counterId, maxLength) {
            var input = document.getElementById(inputId);
            var counter = document.getElementById(counterId);
            if (input && counter) {
                function updateCount() {
                    var length = input.value.length;
                    counter.textContent = length;
                    if (length > maxLength) {
                        counter.classList.add('text-red-500');
                        counter.classList.remove('text-gray-500');
                    } else if (length > maxLength * 0.9) {
                        counter.classList.add('text-orange-500');
                        counter.classList.remove('text-gray-500', 'text-red-500');
                    } else {
                        counter.classList.add('text-gray-500');
                        counter.classList.remove('text-red-500', 'text-orange-500');
                    }
                }
                input.addEventListener('input', updateCount);
                updateCount(); // Initial count
            }
        }

        // Initialize SEO counters if elements exist
        setupCharCounter('seo_title', 'seo_title_count', 70);
        setupCharCounter('meta_description', 'meta_description_count', 160);
    </script>
</body>
</html>
