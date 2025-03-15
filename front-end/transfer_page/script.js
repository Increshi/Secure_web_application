// Fetch user data on page load
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('search-query').value = '';
    document.getElementById('amount').value = '';
    document.getElementById('receiver-id').value = '';
    document.getElementById('comment').value = '';
    document.getElementById('transaction-list').innerHTML = ''; // Clear transaction history
});

// Search users by username or user ID
document.getElementById('search-btn').addEventListener('click', () => {
    const searchQuery = document.getElementById('search-query').value.trim();
    if (searchQuery) {
        searchUsers(searchQuery);
    } else {
        alert("Please enter a search query.");
    }
});

// Function to search users
function searchUsers(query) {
    const apiUrl = `http://localhost:8080/index.php?request=search_user&query=${query}`;

    const token = sessionStorage.getItem('auth_token');

    if (!token) {
        alert("Unauthorized: Missing token");
        return;
    }
    
    fetch(apiUrl, {
        method: 'GET',
        headers: {
            'Authorization': `Bearer ${token}`,
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error("Error fetching search results.");
        }
        return response.json();
    })
    .then(data => {
        if (data.error) {
            alert(`Error: ${data.error}`);
        } else {
            displaySearchResults(data);
        }
    })
    .catch(error => {
        console.error("Error fetching search results:", error);
    });
}

// Display search results
function displaySearchResults(users) {
    const resultsDiv = document.getElementById('search-results');
    resultsDiv.innerHTML = ''; // Clear previous results

    if (users.length === 0) {
        resultsDiv.innerHTML = '<p>No users found.</p>';
        return;
    }

    users.forEach(user => {
        const userDiv = document.createElement('div');
        userDiv.classList.add('search-result-item');
        userDiv.innerHTML = `
            <p><strong>${user.username}</strong> (Email: ${user.email})</p>
            <button onclick="setReceiver('${user.username}')">Select</button>
        `;
        resultsDiv.appendChild(userDiv);
    });
}

// Set selected user as receiver for transfer
function setReceiver(userName) {
    document.getElementById('receiver-id').value = userName;
    alert(`Selected receiver: ${userName}`);
}

// Handle money transfer
document.getElementById('transfer-btn').addEventListener('click', () => {
    const amount = parseFloat(document.getElementById('amount').value);
    const receiverId = document.getElementById('receiver-id').value;
    const comment = document.getElementById('comment').value.trim();
    
    if (!receiverId) {
        alert("Please provide a valid receiver ID and amount.");
        return;
    }

    if(!amount || isNaN(amount) || amount <= 0 )
    {
        alert("Please provide a valid amount.");
        return;
    }

    transferMoney(receiverId, amount, comment);
});

// Function to handle money transfer
function transferMoney(receiverId, amount, comment) {
    // Mock API URL for transfer (replace with real API URL)
    const apiUrl = `http://localhost:8080/index.php?request=transfer_money`;

    // Ensure the amount is a positive number
    if (amount <= 0) {
        alert('Amount must be greater than zero.');
        return;
    }

    const data = {
        receiverId: receiverId,
        amount: amount,
        comment: comment
    };

    const token = sessionStorage.getItem('auth_token'); // Or retrieve it from wherever you're storing the token
    if (!token) {
        alert("Unauthorized: Missing token");
        return;
    }

    fetch(apiUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`, // Authorization header
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Transfer successful!');
            displayTransactionHistory(data.transactionHistory);
            // fetchTransactionHistory(data.user);
        } else {
            alert('Transfer failed: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error transferring money:', error);
    });
}

// // Fetch transaction history
// function fetchTransactionHistory(userId) {
//     fetch(`http://localhost:8080/index.php?request=transaction_history&user_id=${userId}`)
//         .then(response => response.json())
//         .then(data => {
//             if (data.error) {
//                 console.error('Error fetching transaction history:', data.error);
//                 alert('Error: Could not load transaction history.');
//                 return;
//             }

//             // Display the transaction history
//             displayTransactionHistory(data.transactions);
//         })
//         .catch(error => {
//             console.error('Error fetching transaction history:', error);
//             alert('Error: Could not fetch transaction history.');
//         });
// }

// Display transaction history
function displayTransactionHistory(transactions) {
    const historyList = document.getElementById('transaction-list');
    historyList.innerHTML = ''; // Clear previous history

    if (transactions.length === 0) {
        historyList.innerHTML = '<li>No transaction history available.</li>';
        return;
    }

    transactions.forEach(transaction => {
        const transactionItem = document.createElement('li');
        transactionItem.innerHTML = `
            <strong>To: ${transaction.receiver}</strong><br>
            Amount: Rs. ${transaction.amount}<br>
            Comment: ${transaction.comment || 'No comment'}<br>
            Date: ${new Date(transaction.date).toLocaleString()}
        `;
        historyList.appendChild(transactionItem);
    });
}

// Go back to the previous page
function goBack() {
    window.history.back();
}
