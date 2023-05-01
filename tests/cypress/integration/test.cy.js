/// <reference types="cypress" />

context(
	'Hide from Search Meta Box',
	() => {

		before(() => {
			cy.visit('/wp-admin/post.php?post=1&action=edit');
			cy
				.window()
				.then((win) => {
					const {wp} = win;
					if (wp.data && wp.data.select('core/edit-post').isFeatureActive('welcomeGuide')) {
						wp.data.dispatch('core/edit-post').toggleFeature('welcomeGuide');
					}
				});
			cy
				.document()
				.then((doc) => {
					doc.querySelector('.components-modal__screen-overlay').style.display = 'none';
				});
		})

		it('Is Accessible', () => {
			cy.injectAxe();
			cy.checkA11y('#hide-from-search .inside');
		})

		it('Should be visible', () => {
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
