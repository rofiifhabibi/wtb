<div id="toast-container" class="fixed top-5 right-5 z-[100] space-y-3 pointer-events-none"></div>

<style>
    .toast-card {
        transform: translateX(150%);
        transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        opacity: 0;
    }
    .toast-card.show {
        transform: translateX(0);
        opacity: 1;
        pointer-events: auto;
    }
    .toast-card.hide {
        transform: translateX(150%);
        opacity: 0;
    }
</style>

<script>
function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    if(!container) return;

    const toast = document.createElement('div');
    const isSuccess = type === 'success';
    
    toast.className = `toast-card ${isSuccess ? 'bg-slate-900' : 'bg-red-600'} text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-4 min-w-[320px] border border-white/10`;
    
    toast.innerHTML = `
        <div class="flex-shrink-0 ${isSuccess ? 'text-blue-400' : 'text-white'}">
            ${isSuccess ? 
                '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>' : 
                '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>'}
        </div>
        <div class="flex-1">
            <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-50 mb-0.5">${type}</p>
            <p class="text-sm font-bold leading-tight">${message}</p>
        </div>
    `;

    container.appendChild(toast);
    setTimeout(() => toast.classList.add('show'), 100);

    setTimeout(() => {
        toast.classList.add('hide');
        setTimeout(() => toast.remove(), 500);
    }, 4000);
}

<?php if(isset($_SESSION['msg'])): ?>
    document.addEventListener('DOMContentLoaded', function() {
        showToast("<?= addslashes($_SESSION['msg']['text']) ?>", "<?= $_SESSION['msg']['type'] ?>");
    });
    <?php unset($_SESSION['msg']); ?>
<?php endif; ?>
</script>