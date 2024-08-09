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
                        localStorage.setItem('tutorialSteps', JSON.stringify(loginSteps));
                        localStorage.setItem('currentStep', '0');
                        window.location.href = './templates/login.php';
                    }
                }
            ];
            break;
        case 'Where to Check Your License':
            // Add steps for the license checking tutorial
            break;
        case 'Calculating Your Total':
            // Add steps for the total calculation tutorial
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
    }).oncomplete(function() {
        let redirectUrl = '';
        switch (cardTitle) {
            case 'How to Login':
                redirectUrl = './templates/login.php';
                break;
            case 'Where to Check Your License':
                redirectUrl = './templates/license.php';
                break;
            case 'Calculating Your Total':
                redirectUrl = './templates/billing.php';
                break;
        }

        // Save the initial steps for the redirection tutorial in local storage
        localStorage.setItem('tutorialSteps', JSON.stringify(steps));
        localStorage.setItem('currentStep', '1'); // The step after redirection

        window.location.href = redirectUrl;
    }).start();
}
