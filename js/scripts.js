function startTutorial(cardId) {
  const card = document.getElementById(cardId);
  const cardTitle = card.querySelector('h3').textContent;

  let steps = [];

  switch (cardTitle) {
      case 'How to Login':
          steps = [
              {
                  intro: "Welcome to the NEMA Customer Care System! Let's guide you through the login process."
              },
              {
                  intro: "You will now be redirected to the login page to complete the process."
              }
          ];

          const loginSteps = [
              {
                  intro: "Enter your username here.",
                  element: '#username',
                  position: 'right'
              },
              {
                  intro: "Enter your password here.",
                  element: '#password',
                  position: 'right'
              },
              {
                  intro: "Finally, click the 'Login' button to log in to your account.",
                  element: '#loginForm button[type="submit"]',
                  position: 'right'
              }
          ];

          const tutorialSteps = JSON.parse(localStorage.getItem('tutorialSteps')) || [];
          const completeSteps = tutorialSteps.concat(loginSteps);
          localStorage.setItem('tutorialSteps', JSON.stringify(completeSteps));
          localStorage.setItem('currentStep', tutorialSteps.length); // Set current step to the first login step

          window.location.href = './templates/login.php';
          break;

      case 'Where to Check Your License':
          steps = [

              {
                  intro: "You will now be redirected to the license section to apply for your license."
              }
          ];

          const checkLicenseSteps = [
              {
                  intro: "Click here to start the application process.",
                  element: '#apply-btn',
                  position: 'right'
              },
              {
                  intro: "Select license type.",
                  element: '#license_type',
                  position: 'right'
              },
              {
                  intro: "Upload required documents.",
                  element: '#documents',
                  position: 'right'
              },
              {
                  intro: "Here, you can view the status of all your licenses.",
                  element: '#licenseStatusSection',
                  position: 'right'
              }
          ];

          const licenseTutorialSteps = JSON.parse(localStorage.getItem('tutorialSteps')) || [];
          const completeLicenseSteps = licenseTutorialSteps.concat(checkLicenseSteps);
          localStorage.setItem('tutorialSteps', JSON.stringify(completeLicenseSteps));
          localStorage.setItem('currentStep', licenseTutorialSteps.length); // Set current step to the first check license step

          // Redirect to licensing portal
          window.location.href = './templates/licensing_portal.php';
          break;

      case 'Calculating Your Total':
          // Steps for "Calculating Your Total"
          break;

      default:
          console.error('No tutorial found for card:', cardTitle);
  }

  introJs().setOptions({
      steps: steps,
      nextLabel: 'Next',
      prevLabel: 'Previous',
      skipLabel: 'Skip',
      doneLabel: 'Finish'
  }).start();
}
