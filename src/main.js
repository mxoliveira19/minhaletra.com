// Import main stylesheet so Vite compiles it
import './style.css';

document.addEventListener('DOMContentLoaded', () => {
    const resizeTitleField = (field) => {
        field.style.height = 'auto';
        field.style.height = `${field.scrollHeight}px`;
    };

    // 1. Mobile Menu Toggle
    const navToggle = document.getElementById('navToggle');
    const navbar = document.getElementById('navbar');

    if (navToggle && navbar) {
        navToggle.addEventListener('click', () => {
            navbar.classList.toggle('active');
            navToggle.classList.toggle('active');
            
            // Toggle hamburger animation
            const bars = navToggle.querySelectorAll('.bar');
            if (navToggle.classList.contains('active')) {
                bars[0].style.transform = 'rotate(-45deg) translate(-5px, 6px)';
                bars[1].style.opacity = '0';
                bars[2].style.transform = 'rotate(45deg) translate(-5px, -6px)';
            } else {
                bars[0].style.transform = 'none';
                bars[1].style.opacity = '1';
                bars[2].style.transform = 'none';
            }
        });
    }

    // 2. Admin: Toggle New Text Form
    const btnToggleNewForm = document.getElementById('btnToggleNewForm');
    const btnCancelNewForm = document.getElementById('btnCancelNewForm');
    const newTextFormCard = document.getElementById('newTextFormCard');

    if (btnToggleNewForm && newTextFormCard) {
        btnToggleNewForm.addEventListener('click', () => {
            newTextFormCard.classList.toggle('hidden');
            if (!newTextFormCard.classList.contains('hidden')) {
                newTextFormCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
                // Focus title input
                const titleInput = newTextFormCard.querySelector('input[name="titulo"]');
                if (titleInput) titleInput.focus();
            }
        });
    }

    if (btnCancelNewForm && newTextFormCard) {
        btnCancelNewForm.addEventListener('click', () => {
            newTextFormCard.classList.add('hidden');
        });
    }

    // 3. Admin: Toggle Expanded Text Editor per Row
    const btnToggleEditors = document.querySelectorAll('.btn-toggle-editor');
    btnToggleEditors.forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            const editorPane = document.getElementById(`editor-${id}`);
            if (editorPane) {
                const isCollapsed = editorPane.classList.contains('collapsed');
                
                // Collapse any other open editors to keep clean view
                document.querySelectorAll('.row-editor-expanded').forEach(pane => {
                    if (pane.id !== `editor-${id}`) {
                        pane.classList.add('collapsed');
                        const otherId = pane.id.split('-')[1];
                        const otherBtn = document.querySelector(`.btn-toggle-editor[data-id="${otherId}"]`);
                        if (otherBtn) otherBtn.classList.remove('active');
                    }
                });

                editorPane.classList.toggle('collapsed');
                btn.classList.toggle('active');
                
                if (isCollapsed) {
                    // Focus the text area
                    const textarea = editorPane.querySelector('textarea');
                    if (textarea) textarea.focus();
                }
            }
        });
    });

    const btnCloseEditors = document.querySelectorAll('.btn-close-editor');
    btnCloseEditors.forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            const editorPane = document.getElementById(`editor-${id}`);
            const toggleBtn = document.querySelector(`.btn-toggle-editor[data-id="${id}"]`);
            if (editorPane) {
                editorPane.classList.add('collapsed');
            }
            if (toggleBtn) {
                toggleBtn.classList.remove('active');
            }
        });
    });

    // 4. Admin: AJAX Save and Auto-Save for changes
    const adminRowForms = document.querySelectorAll('.admin-row-form');
    adminRowForms.forEach(form => {
        const id = form.getAttribute('data-id');
        const statusMsg = document.getElementById(`status-msg-${id}`);

        // Handle full edit form submission
        form.addEventListener('submit', (e) => {
            e.preventDefault();

            if (statusMsg) {
                statusMsg.textContent = 'Salvando...';
                statusMsg.className = 'save-status saving';
            }

            const formData = new FormData(form);
            formData.append('ajax', '1');

            fetch('/admin/editar', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Erro na requisição');
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    if (statusMsg) {
                        statusMsg.textContent = '✓ Salvo';
                        statusMsg.className = 'save-status success';
                        setTimeout(() => {
                            statusMsg.textContent = '';
                        }, 2500);
                    }

                    // Update visible preview text
                    const textarea = form.querySelector('.expanded-textarea');
                    const previewBox = form.querySelector('.preview-text-box');
                    const tipoInput = form.querySelector('input[name="tipo"]');
                    if (textarea && previewBox && tipoInput) {
                        const text = textarea.value;
                        const tipo = tipoInput.value;
                        let preview = '';
                        
                        if (tipo === 'poesias') {
                            preview = text.split('\n').slice(0, 5).join('\n');
                        } else if (tipo === 'frases') {
                            preview = text;
                        } else {
                            preview = text.length > 300 ? text.substring(0, 300) + '...' : text;
                        }
                        
                        previewBox.innerHTML = escapeHtml(preview).replace(/\n/g, '<br>');
                    }
                } else {
                    if (statusMsg) {
                        statusMsg.textContent = 'Erro ao salvar';
                        statusMsg.className = 'save-status error';
                    }
                }
            })
            .catch(err => {
                console.error(err);
                if (statusMsg) {
                    statusMsg.textContent = 'Erro de conexão';
                    statusMsg.className = 'save-status error';
                }
            });
        });

        // Auto-save inline inputs (Title, Mode, Weight) on change
        const inlineInputs = form.querySelectorAll('.inline-input-title, .inline-select-modo, .inline-input-peso');
        inlineInputs.forEach(input => {
            if (input.classList.contains('inline-input-title')) {
                resizeTitleField(input);
                input.addEventListener('input', () => resizeTitleField(input));
            }

            input.addEventListener('change', () => {
                if (typeof form.requestSubmit === 'function') {
                    form.requestSubmit();
                } else {
                    const event = new Event('submit', { cancelable: true });
                    form.dispatchEvent(event);
                }
            });
        });
    });

    // 5. Admin: Confirm Delete Dialog
    const confirmDeleteLinks = document.querySelectorAll('.btn-confirm-delete');
    confirmDeleteLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            const confirmed = confirm('Tem certeza que deseja deletar este texto permanentemente? Esta ação não pode ser desfeita.');
            if (!confirmed) {
                e.preventDefault();
            }
        });
    });
});

/**
 * Escapes HTML characters to prevent XSS.
 */
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}
