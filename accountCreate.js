async function addUsersFromCSV(file) {
    const reader = new FileReader();
    reader.readAsText(file);
    
    reader.onload = async function (event) {
        const csvData = event.target.result;
        const lines = csvData.split('\n');
        
        for (let i = 1; i < lines.length; i++) { // Assuming first line is headers
            const values = lines[i].split(',');
            if (values.length < 4) continue; // Skip if row doesn't have enough columns
            
            const userData = {
                fullname: values[0].trim(),
                username: values[1].trim(),
                email: values[2].trim(),
                password: values[3].trim()
            };

            try {
                const response = await fetch(`${apiBaseUrl}?request=register`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(userData),
                });
                
                const data = await response.json();
                console.log(`User ${userData.username} registration response:`, data);
            } catch (error) {
                console.error(`Error registering user ${userData.username}:`, error);
            }
        }
    };
}


addUsersFromCSV(hello.csv);



