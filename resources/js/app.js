import './bootstrap';

const syncSidebarTheme = () => {
	const theme = document.documentElement.getAttribute('data-bs-theme');

	if (!theme) {
		return;
	}

	document.documentElement.setAttribute(
		'data-sidebar',
		theme === 'dark' ? 'dark-sidebar' : 'light-sidebar'
	);
};

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', syncSidebarTheme, { once: true });
} else {
	syncSidebarTheme();
}

new MutationObserver((mutations) => {
	if (mutations.some((mutation) => mutation.attributeName === 'data-bs-theme')) {
		syncSidebarTheme();
	}
}).observe(document.documentElement, {
	attributes: true,
	attributeFilter: ['data-bs-theme'],
});
