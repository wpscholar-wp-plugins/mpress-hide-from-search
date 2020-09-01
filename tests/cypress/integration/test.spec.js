/// <reference types="cypress" />

context(
	'Hide from Search Meta Box',
	() => {

		function loadPage() {
			cy.visit('/wp-admin/post.php?post=1&action=edit');
			cy.maybeDismissGutenbergWelcomeModal();
			cy.document().then(doc => {
				doc.getElementById('_wpnonce')
			})
		}

		before(() => {
			loadPage();
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
				.findByRole('button', {name: 'Toggle panel: Hide from Search'})
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
				.findByLabelText('Hide from WordPress search')
				.scrollIntoView()
				.should('be.visible');
		})

		it('Should have "Hide from search engines" toggle', () => {
			cy
				.findByLabelText('Hide from search engines')
				.scrollIntoView()
				.should('be.visible');
		})

		it.skip('Should save', () => {

			// Listen for the API call to save the post
			cy.server();
			cy.route('GET', '**/wp/v2/posts/1*').as('fetch');
			cy.route('POST', '**/wp/v2/posts/1*').as('update');

			// Check checkboxes
			cy.findByLabelText('Hide from WordPress search').scrollIntoView().check();
			cy.findByLabelText('Hide from search engines').scrollIntoView().check();

			// Save
			cy.findByRole('button', {name: 'Update'}).click();
			cy.wait('@update');

			// Reload page
			loadPage();
			cy.wait('@fetch');

			// Checkboxes should be checked
			cy.findByLabelText('Hide from WordPress search').should('be.checked');
			cy.findByLabelText('Hide from search engines').should('be.checked');

			// Uncheck boxes
			cy.findByLabelText('Hide from WordPress search').scrollIntoView().uncheck();
			cy.findByLabelText('Hide from search engines').scrollIntoView().uncheck();

			// Save
			cy.findByRole('button', {name: 'Update'}).click();
			cy.wait('@update');

			// Reload page
			loadPage();
			cy.wait('@fetch');

			// Checkboxes should be unchecked
			cy.findByLabelText('Hide from WordPress search').should('not.be.checked');
			cy.findByLabelText('Hide from search engines').should('not.be.checked');

		})

	}
);
