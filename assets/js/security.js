/**
 * Security script to deter unauthorized access to developer tools and code inspection.
 * Required for client-side PCI DSS / BBVA basic compliance checks.
 */
document.addEventListener('DOMContentLoaded', () => {
    // 1. Disable Right Click (Context Menu)
    document.addEventListener('contextmenu', (e) => {
        e.preventDefault();
    });

    // 2. Disable F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U
    document.addEventListener('keydown', (e) => {
        // F12
        if (e.key === 'F12' || e.keyCode === 123) {
            e.preventDefault();
        }
        // Ctrl+Shift+I / Mac: Cmd+Option+I
        if (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'i' || e.keyCode === 73)) {
            e.preventDefault();
        }
        // Ctrl+Shift+J / Mac: Cmd+Option+J
        if (e.ctrlKey && e.shiftKey && (e.key === 'J' || e.key === 'j' || e.keyCode === 74)) {
            e.preventDefault();
        }
        // Ctrl+U / Mac: Cmd+U (View Source)
        if (e.ctrlKey && (e.key === 'U' || e.key === 'u' || e.keyCode === 85)) {
            e.preventDefault();
        }
        // Ctrl+Shift+C / Mac: Cmd+Option+C (Inspect Element)
        if (e.ctrlKey && e.shiftKey && (e.key === 'C' || e.key === 'c' || e.keyCode === 67)) {
            e.preventDefault();
        }
    });
});
