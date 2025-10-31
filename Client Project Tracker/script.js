// Tabs Logic for Login/Register Page

document.addEventListener('click', (e) => { // Listen for clicks anywhere on the document
  if (!e.target.classList.contains('tab')) return;// Exit if the clicked element is not a tab button
  document.querySelectorAll('.tab').forEach((tab) => {  // Remove the "active" class from all tabs
    tab.classList.remove('active');
  });

  e.target.classList.add('active'); // Add "active" class to the clicked tab

  const targetTab = e.target.dataset.tab; // Get the ID of the related tab pane from the clicked tab's data attribute

  document.querySelectorAll('.tab-pane').forEach((pane) => { // Hide all tab panes
    pane.classList.remove('active');
  });

  document.getElementById(targetTab).classList.add('active'); // Show the selected tab pane
});
