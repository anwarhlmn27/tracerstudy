document.addEventListener('DOMContentLoaded', function() {
    function initTS(el) {
        if (el.tomselect || el.classList.contains('ts-hidden-accessible') || el.classList.contains('no-ts')) return;
        if (el.closest('.dataTables_length')) return;
        if (el.closest('.ql-toolbar')) return;

        new TomSelect(el, {
            create: false,
            plugins: el.hasAttribute('multiple') ? ['remove_button'] : [],
            onChange: function(value) {
                // Trigger native events for Alpine and onchange listeners
                el.dispatchEvent(new Event('input', { bubbles: true }));
                el.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
    }

    // Initialize all existing selects
    document.querySelectorAll('select').forEach(initTS);
    
    // Observer for modals and dynamic content
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            // For <dialog> modal opens
            if (mutation.type === 'attributes' && mutation.attributeName === 'open') {
                if (mutation.target.hasAttribute('open')) {
                    mutation.target.querySelectorAll('select').forEach(initTS);
                }
            }
            // For added elements like Alpine x-for loops
            if (mutation.type === 'childList') {
                mutation.addedNodes.forEach(node => {
                    if (node.nodeType === 1) { // Element node
                        if (node.tagName === 'SELECT') initTS(node);
                        else node.querySelectorAll('select').forEach(initTS);
                    }
                });
            }
        });
    });

    observer.observe(document.body, { childList: true, subtree: true, attributes: true, attributeFilter: ['open'] });
});
