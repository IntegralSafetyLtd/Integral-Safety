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
    </script>
</body>
</html>
