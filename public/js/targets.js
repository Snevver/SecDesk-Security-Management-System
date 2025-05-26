function fetchTestTargets(test_id) {
    console.log(`Fetching targets for test ID: ${test_id}`);
    fetch(`/api/targets?test_id=${test_id}`)
        .then(response => {
            if (!response.ok) {
                console.log(response);	
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Test targets:', data.targets);
            const targetListElement = document.getElementsByClassName("target-list")[0];
            if (!data || !data.targets || data.targets.length === 0) {
                targetListElement.innerHTML = "<p>No targets found.</p>";
                return;
            }
            let targetList = "";
            for (let target of data.targets) {
                console.log(`Target: ${target.target_name}`);
                console.log(`Description: ${target.target_description}`);

                // DOOOOOON succes met stylen mattie
                targetList += `
                    <div>
                        <h2>
                            ${target.target_name}
                        </h2>
                        <div>
                            <p>${target.target_description}</p>
                        </div>
                    </div>`;
            }

            targetListElement.innerHTML = targetList;
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
        });
}


const urlParams = new URLSearchParams(window.location.search);
const test_id = urlParams.get('test_id');
fetchTestTargets(test_id);