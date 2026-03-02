/// <reference types="cypress" />

context(
	'Hide from Search Meta Box',
	() => {

		function loadPage() {
			cy.visit('/wp-admin/post.php?post=1&action=edit');
			// Wait for the meta boxes to load via the AJAX request.
			cy.get('#hide-from-search', { timeout: 15000 });
			// WP 6.8+ introduced a ResizableMetaBoxesArea with a fixed default height that
			// clips meta box content. React manages this via inline styles (e.g. style="height:300px"),
			// so element.style.height = 'auto' gets overridden on the next render.
			// Injecting a <style> tag with !important wins the CSS cascade against any
			// non-!important inline style React sets, and persists across re-renders.
			cy.document().then((doc) => {
				if (!doc.getElementById('cypress-meta-box-fix')) {
					const style = doc.createElement('style');
					style.id = 'cypress-meta-box-fix';
					style.textContent = '.edit-post-meta-boxes-area { height: auto !important; max-height: none !important; overflow: visible !important; }';
					doc.head.appendChild(style);
				}
			});
		}

		function dismissModal() {
			cy
				.window()
				.then((window) => {
					const {wp} = window;
					if (wp.data && wp.data.select('core/edit-post').isFeatureActive('welcomeGuide')) {
						wp.data.dispatch('core/edit-post').toggleFeature('welcomeGuide');
					}
				});
			cy
				.document()
				.then((document) => {
					const overlay = document.querySelector('.components-modal__screen-overlay');
					if (overlay) {
						overlay.style.display = 'none';
					}
				});
		}

		beforeEach(() => {
			loadPage();
		})

		it('Is Accessible', () => {
			cy.injectAxe();
			cy.checkA11y('#hide-from-search .inside');
		})

		it('Should be visible', () => {
			dismissModal();
			cy
				.get('#hide-from-search')
				.scrollIntoView()
				.should('be.visible');
		})

		it('Should be expanded', () => {
			cy
				.get('#hide-from-search button.handlediv')
				.within(($btn) => {
					if ($btn.attr('aria-expanded') === 'false') {
						cy.root().click();
					}
					cy
						.root()
						.should('have.attr', 'aria-expanded')
						.and('equal', 'true');
				});
		})

		it('Should have "Hide from WordPress search" toggle', () => {
			cy
				.get('#hide-from-search input[type="checkbox"][name="_hide_from_search_wp"]')
				.scrollIntoView()
				.should('be.visible');
		})

		it('Should have "Hide from search engines" toggle', () => {
			cy
				.get('#hide-from-search input[type="checkbox"][name="_hide_from_search_engines"]')
				.scrollIntoView()
				.should('be.visible');
		})

		it('Should save', () => {

			cy.get('#hide-from-search input[type="checkbox"][name="_hide_from_search_wp"]').as('wpCheckbox');
			cy.get('#hide-from-search input[type="checkbox"][name="_hide_from_search_engines"]').as('searchCheckbox');

			// Listen for the POST request to save the post meta
			cy.intercept({
				method: 'POST',
				url: '**post.php?post=1&action=edit&meta-box-loader=1*'
			}).as('meta');

			// Check checkboxes
			cy.get('@wpCheckbox').scrollIntoView().check();
			cy.get('@searchCheckbox').scrollIntoView().check();

			// Save and reload
			cy.get('.editor-post-publish-button').click();
			cy.wait('@meta');
			loadPage();

			// Checkboxes should be checked
			cy.get('@wpCheckbox').should('be.checked');
			cy.get('@searchCheckbox').should('be.checked');

			// Uncheck boxes
			dismissModal();
			cy.get('@wpCheckbox').scrollIntoView().uncheck();
			cy.get('@searchCheckbox').scrollIntoView().uncheck();

			// Save and reload
			cy.get('.editor-post-publish-button').click();
			cy.wait('@meta');
			loadPage();

			// Checkboxes should be unchecked
			cy.get('@wpCheckbox').should('not.be.checked');
			cy.get('@searchCheckbox').should('not.be.checked');

		})

	}
);
