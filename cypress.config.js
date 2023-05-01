const {defineConfig} = require('cypress')

module.exports = defineConfig({
	env: {
		wpUsername: 'admin',
		wpPassword: 'password',
	},
	downloadsFolder: 'tests/cypress/downloads',
	fixturesFolder: 'tests/cypress/fixtures',
	screenshotsFolder: 'tests/cypress/screenshots',
	videosFolder: 'tests/cypress/videos',
	videoUploadOnPasses: false,
	e2e: {
		setupNodeEvents(on, config) {

			// Ensure that the base URL is always properly set.
			if (config.env && config.env.baseUrl) {
				config.baseUrl = config.env.baseUrl;
			}

			return config;
		},
		baseUrl: 'http://localhost:8888',
		specPattern: 'tests/cypress/integration/**/*.cy.{js,jsx,ts,tsx}',
		supportFile: 'tests/cypress/support/index.js',
		testIsolation: false
	},
})
