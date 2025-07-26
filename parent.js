document.addEventListener("DOMContentLoaded", function () {
  // === Sidebar & Page Navigation ===
  const menuItems = document.querySelectorAll(".sidebar-menu li[data-section]");
  const sections = document.querySelectorAll(".content-section");
  const pageTitle = document.getElementById('pageTitle');

  sections.forEach(section => {
    if (section.id === "child-details") {
      section.style.display = "block";
      section.classList.add("active");
    } else {
      section.style.display = "none";
      section.classList.remove("active");
    }
  });

  function showSection(sectionId) {
    sections.forEach(section => {
      if (section.id === sectionId) {
        section.style.display = "block";
        section.classList.add("active");
      } else {
        section.style.display = "none";
        section.classList.remove("active");
      }
    });

    const activeMenuItem = document.querySelector(`.sidebar-menu li[data-section="${sectionId}"]`);
    if (activeMenuItem) {
      const label = activeMenuItem.querySelector("span");
      pageTitle.textContent = label ? label.textContent : "Dashboard";
    }
  }

  menuItems.forEach(item => {
    const link = item.querySelector("a");
    if (link) {
      link.addEventListener("click", function (e) {
        e.preventDefault();
        menuItems.forEach(el => el.classList.remove("active"));
        item.classList.add("active");
        const sectionId = item.getAttribute("data-section");
        showSection(sectionId);
      });
    }
  });

  // === User Profile Dropdown ===
  const userProfile = document.getElementById("userProfile");
  const userDropdown = document.getElementById("userDropdown");

  if (userProfile && userDropdown) {
    userProfile.addEventListener("click", function (e) {
      e.stopPropagation();
      userDropdown.classList.toggle("show");
    });

    document.addEventListener("click", function (event) {
      if (!userProfile.contains(event.target)) {
        userDropdown.classList.remove("show");
      }
    });
  }

  // === Add Child Form Toggle ===
  const openBtn = document.getElementById('openChildFormBtn');
  const cancelBtn = document.getElementById('cancelChildFormBtn');
  const form = document.getElementById('childForm');

  if (openBtn && cancelBtn && form) {
    openBtn.addEventListener('click', () => {
      form.style.display = 'block';
    });

    cancelBtn.addEventListener('click', () => {
      form.style.display = 'none';
    });
  }

  // === Child Record Display & Filtering ===
  const childNameElements = document.querySelectorAll('.child-name-select');
  const vaccinationTableBody = document.getElementById('vaccinationTableBody');
  const allRows = Array.from(vaccinationTableBody.querySelectorAll('tr'));

  const childDetailsContainer = document.createElement('div');
  childDetailsContainer.className = 'child-details-container';
  childDetailsContainer.innerHTML = `
    <div class="child-profile">
      <img id="childImage" src="" alt="Child Image" class="child-image">
      <div class="child-info">
        <h2 id="childName"></h2>
        <p><strong>Age:</strong> <span id="childAge"></span></p>
        <p><strong>Gender:</strong> <span id="childGender"></span></p>
        <p><strong>Date of Birth:</strong> <span id="childDob"></span></p>
      </div>
    </div>
  `;
  document.querySelector('.child-names').after(childDetailsContainer);
  childDetailsContainer.style.display = 'none';

  const childImage = childDetailsContainer.querySelector('#childImage');
  const childName = childDetailsContainer.querySelector('#childName');
  const childAge = childDetailsContainer.querySelector('#childAge');
  const childGender = childDetailsContainer.querySelector('#childGender');
  const childDob = childDetailsContainer.querySelector('#childDob');

  function showAllRecords() {
    allRows.forEach(row => {
      row.style.display = '';
    });
    childDetailsContainer.style.display = 'none';
    childNameElements.forEach(el => el.classList.remove('active'));
  }

  function showChildRecords(childElement) {
    const childId = childElement.dataset.id;
    const name = childElement.dataset.name;
    const age = childElement.dataset.age;
    const gender = childElement.dataset.gender;
    const dob = childElement.dataset.dob;
    const image = childElement.dataset.image;

    childName.textContent = name;
    childAge.textContent = age;
    childGender.textContent = gender;
    childDob.textContent = dob;
    childImage.src = image || 'default-child-image.jpg';
    childDetailsContainer.style.display = 'block';

    allRows.forEach(row => {
      row.style.display = (row.dataset.childId === childId) ? '' : 'none';
    });

    childNameElements.forEach(el => el.classList.remove('active'));
    childElement.classList.add('active');
  }

  childNameElements.forEach(childElement => {
    childElement.addEventListener('click', function () {
      if (this.classList.contains('active')) {
        showAllRecords();
      } else {
        showChildRecords(this);
      }
    });
  });

  showAllRecords();

// Chatbot Section
const openChatBtn = document.getElementById('open-chat-btn');
const chatContainer = document.getElementById('chat-container');
const closeBtn = document.getElementById('close-btn');
const input = document.getElementById('user-input');
const sendBtn = document.getElementById('send-btn');
const chatBox = document.getElementById('chat-box');
const childID = document.getElementById('child-id')?.value || null;

if (openChatBtn && chatContainer && closeBtn && input && sendBtn && chatBox) {
  openChatBtn.addEventListener('click', () => {
    chatContainer.style.display = 'flex';
    openChatBtn.style.display = 'none';
  });

  closeBtn.addEventListener('click', () => {
    chatContainer.style.display = 'none';
    openChatBtn.style.display = 'block';
  });

  sendBtn.addEventListener('click', sendMessage);
  input.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') sendMessage();
  });

  function sendMessage() {
    const message = input.value.trim();
    if (!message || !childID) return;

    appendMessage('You', message);
    input.value = '';

    fetch('chatbot.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ question: message, child_id: childID })
    })
      .then(res => res.json())
      .then(data => appendBotMessage(data.reply))
      .catch(() => appendBotMessage('⚠️ Error: Unable to fetch response.'));
  }

  function appendMessage(sender, message) {
    const messageElement = document.createElement('div');
    messageElement.className = 'message';
    messageElement.innerHTML = `<strong>${sender}:</strong> ${message}`;
    chatBox.appendChild(messageElement);
    chatBox.scrollTop = chatBox.scrollHeight;
  }

  // New function for bot messages with voice button
  function appendBotMessage(message) {
  const messageElement = document.createElement('div');
  messageElement.className = 'message';
  messageElement.innerHTML = `
    <strong>Bot:</strong> ${message}
    <i class="fas fa-microphone" style="cursor: pointer; margin-left: 10px; color: #1f1fd3;" onclick="speakText(\`${message.replace(/`/g, '\\`')}\`)"></i>
  `;
  chatBox.appendChild(messageElement);
  chatBox.scrollTop = chatBox.scrollHeight;
}

window.speakText = function (text) {
  // Stop current speech (if any)
  if (speechSynthesis.speaking || speechSynthesis.pending) {
    speechSynthesis.cancel();
  }

  // Create and speak new text
  const speech = new SpeechSynthesisUtterance(text);
  speech.lang = 'en-US';
  speech.volume = 1;
  speech.rate = 1;
  speech.pitch = 1;

  window.speechSynthesis.speak(speech);
};
};
  // Print code of js
  let selectedChildId = null;

  document.querySelectorAll('.child-name-select').forEach(span => {
    span.addEventListener('click', () => {
      selectedChildId = span.dataset.id;
      document.querySelectorAll('#vaccinationTableBody tr').forEach(row => {
        row.style.display = (row.dataset.childId === selectedChildId) ? '' : 'none';
      });
    });
  });

  window.printSelected = function () {
    if (!selectedChildId) {
      alert("Please select a child first.");
      return;
    }
    window.print();
  };

  window.printAll = function () {
    selectedChildId = null;
    document.querySelectorAll('#vaccinationTableBody tr').forEach(row => row.style.display = '');
    window.print();
  };

  //  Booking js code
  const bookingForm = document.getElementById('bookingForm');

  if (bookingForm) {
    bookingForm.addEventListener('submit', function (e) {
      e.preventDefault();

      const formData = new FormData(bookingForm);

      fetch('book_appointment.php', {
        method: 'POST',
        body: formData
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            alert(data.message);
            window.location.href = 'parent.php';
          } else {
            alert("❌ Error: " + data.message);
          }
        })
        .catch(error => {
          alert("❌ Network error: " + error.message);
        });
    });
  }
});
