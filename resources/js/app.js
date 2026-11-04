const dialog = document.getElementById('confirmation');
let pendingForm = null;
document.querySelectorAll('[data-confirm-delete]').forEach(form => {
    form.addEventListener('submit', event => {
        event.preventDefault(); pendingForm = form; dialog.showModal();
    });
});
document.getElementById('confirm-cancel')?.addEventListener('click', () => {
    pendingForm = null; dialog.close();
});
document.getElementById('confirm-accept')?.addEventListener('click', () => {
    if (pendingForm) { const form = pendingForm; pendingForm = null; dialog.close(); form.submit(); }
});
dialog?.addEventListener('cancel', () => { pendingForm = null; });
document.querySelectorAll('.completion-form').forEach(form => {
    form.addEventListener('submit', () => { form.querySelector('button').disabled = true; });
});
window.addEventListener('pageshow', () => {
    document.querySelectorAll('.completion-form button').forEach(button => { button.disabled = false; });
});
