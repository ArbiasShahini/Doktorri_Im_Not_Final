document.addEventListener("DOMContentLoaded", function () {
  var registerForm = document.getElementById("registerForm");
  if (registerForm) {
    registerForm.addEventListener("submit", function (e) {
      var name = document.getElementById("reg_name").value.trim();
      var email = document.getElementById("reg_email").value.trim();
      var pass = document.getElementById("reg_password").value;
      var confirm = document.getElementById("reg_confirm").value;
      var message = document.getElementById("registerMessage");

      var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      var strong = pass.length >= 8 && /[A-Z]/.test(pass) && /[0-9]/.test(pass) && /[^A-Za-z0-9]/.test(pass);

      if (!name || !email || !pass || !confirm) {
        e.preventDefault();
        message.textContent = "All fields are required.";
        message.className = "message error";
        return;
      }

      if (!emailPattern.test(email)) {
        e.preventDefault();
        message.textContent = "Email is not valid.";
        message.className = "message error";
        return;
      }

      if (!strong) {
        e.preventDefault();
        message.textContent = "Password must be at least 8 characters and include uppercase, number and symbol.";
        message.className = "message error";
        return;
      }

      if (pass !== confirm) {
        e.preventDefault();
        message.textContent = "Passwords do not match.";
        message.className = "message error";
        return;
      }
    });
  }

  var loginForm = document.getElementById("loginForm");
  if (loginForm) {
    loginForm.addEventListener("submit", function (e) {
      var email = document.getElementById("login_email").value.trim();
      var pass = document.getElementById("login_password").value.trim();
      var message = document.getElementById("loginMessage");

      if (!email || !pass) {
        e.preventDefault();
        message.textContent = "Email and password are required.";
        message.className = "message error";
      }
    });
  }

  var feedbackForm = document.getElementById("feedbackForm");
  if (feedbackForm) {
    feedbackForm.addEventListener("submit", function (e) {
      var rating = document.getElementById("fb_rating").value;
      var comment = document.getElementById("fb_comment").value.trim();
      var message = document.getElementById("feedbackMessage");

      if (!rating || !comment) {
        e.preventDefault();
        message.textContent = "Rating and comment are required.";
        message.className = "message error";
      }
    });
  }

  var contactForm = document.getElementById("contactForm");
  if (contactForm) {
    contactForm.addEventListener("submit", function (e) {
      var name = document.getElementById("ct_name").value.trim();
      var email = document.getElementById("ct_email").value.trim();
      var subject = document.getElementById("ct_subject").value.trim();
      var msg = document.getElementById("ct_message").value.trim();
      var message = document.getElementById("contactMessage");

      var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

      if (!name || !email || !subject || !msg) {
        e.preventDefault();
        message.textContent = "All fields are required.";
        message.className = "message error";
        return;
      }

      if (!emailPattern.test(email)) {
        e.preventDefault();
        message.textContent = "Email is not valid.";
        message.className = "message error";
        return;
      }

      message.textContent = "Message sent (frontend demo).";
      message.className = "message success";
      e.preventDefault();
    });
  }

  var doctorList = document.getElementById("doctorList");
  if (doctorList) {
    var doctors = [
      { name: "Dr. Arbias Shahini", spec: "Dentist", desc: "Experienced dentist focused on gentle care." },
      { name: "Dr. Elira K.", spec: "Cardiologist", desc: "Heart specialist with modern approach." },
      { name: "Dr. Besim R.", spec: "Pediatrician", desc: "Takes care of children and families." },
      { name: "Dr. Arta M.", spec: "Dermatologist", desc: "Skin health and cosmetic treatments." },
      { name: "Dr. Endrit L.", spec: "Orthopedic", desc: "Bones and joints specialist." },
      { name: "Dr. Nora S.", spec: "Neurologist", desc: "Brain and nervous system care." }
    ];

    doctors.forEach(function (d) {
      var card = document.createElement("div");
      card.className = "doctor-card";

      card.innerHTML =
        '<div class="doctor-avatar"></div>' +
        '<div class="doctor-name">' + d.name + '</div>' +
        '<div class="doctor-spec">' + d.spec + '</div>' +
        '<div style="font-size:12px;color:#777;margin-bottom:8px;">' + d.desc + '</div>' +
        '<form method="get" action="schedule.php">' +
        '<input type="hidden" name="doctor" value="' + d.name.replace(/"/g, "&quot;") + '">' +
        '<button type="submit">Book appointment</button>' +
        '</form>';

      doctorList.appendChild(card);
    });
  }

  var slider = document.getElementById("heroSlider");
  if (slider) {
    var slides = [
      "linear-gradient(135deg,#d9e4c7,#f5f4eb)",
      "linear-gradient(135deg,#f3dec4,#f5f4eb)",
      "linear-gradient(135deg,#e2dec9,#f5f4eb)"
    ];
    var index = 0;
    slider.style.backgroundImage = slides[index];
    setInterval(function () {
      index = (index + 1) % slides.length;
      slider.style.backgroundImage = slides[index];
    }, 4000);
  }
});
