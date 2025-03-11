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
    const apiUrl = `https://yourapi.com/users/search?q=${query}`;
    
    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            displaySearchResults(data);
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
            <p><strong>${user.name}</strong> (ID: ${user.id})</p>
            <button onclick="setReceiver('${user.id}', '${user.name}')">Select</button>
        `;
        resultsDiv.appendChild(userDiv);
    });
}

// Set selected user as receiver for transfer
function setReceiver(userId, userName) {
    document.getElementById('receiver-id').value = userId;
    alert(`Selected receiver: ${userName}`);
}

// Handle money transfer
document.getElementById('transfer-btn').addEventListener('click', () => {
    const amount = parseFloat(document.getElementById('amount').value);
    const receiverId = document.getElementById('receiver-id').value;
    const comment = document.getElementById('comment').value.trim();
    
    if (!receiverId || !amount || amount <= 0) {
        alert("Please provide a valid receiver ID and amount.");
        return;
    }

    transferMoney(receiverId, amount, comment);
});

// Function to handle money transfer
function transferMoney(receiverId, amount, comment) {
    // Mock API URL for transfer (replace with real API URL)
    const apiUrl = 'https://yourapi.com/transfer';
    const data = {
        receiverId: receiverId,
        amount: amount,
        comment: comment
    };

    fetch(apiUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Transfer successful!');
            displayTransactionHistory(data.transactionHistory);
        } else {
            alert('Transfer failed: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error transferring money:', error);
    });
}

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
