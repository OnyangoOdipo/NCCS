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
            intro: "You will now be redirected to the login page to complete the process.",
            onbeforechange: function() {
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
  
              // Append login steps to existing tutorial steps
              const tutorialSteps = JSON.parse(localStorage.getItem('tutorialSteps')) || [];
              const completeSteps = tutorialSteps.concat(loginSteps);
              localStorage.setItem('tutorialSteps', JSON.stringify(completeSteps));
              localStorage.setItem('currentStep', tutorialSteps.length); // Set current step to the first login step
              window.location.href = './templates/login.php';
            }
          }
        ];
        break;

        case 'How do you apply for your license':
            steps = [
              {
                intro: "Let us guide you on where to apply for your license."
              },
              {
                intro: "You will now be redirected to the license section to apply for your license.",
                onbeforechange: function() {
                  const checkLicenseSteps = [
                    {
                        intro: "Click here to start application process.",
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
          
                  const tutorialSteps = JSON.parse(localStorage.getItem('tutorialSteps')) || [];
                  const completeSteps = tutorialSteps.concat(checkLicenseSteps);
                  localStorage.setItem('tutorialSteps', JSON.stringify(completeSteps));
                  localStorage.setItem('currentStep', tutorialSteps.length); // Set current step to the first check license step
                  window.location.href = './templates/licensing_portal.php'; // Replace with your license portal URL
                }
              }
            ];
            break;          
      case 'Calculating Your Total':
        // ... similar logic for calculating total tutorial ...
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
  