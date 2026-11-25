    const translations = {
      en: {
        page_title: "FIXMYCITY – How to use",
        nav_report: "Report a problem",
        nav_all: "All reports",
        nav_reports: "My reports",
        nav_info: "How to use",
        lang_label: "Language:",
        title: "How to use FIXMYCITY",
        subtitle: "Five simple steps to report a problem in your city.",
        s1_title: "Add a photo",
        s1_text: "Take a picture of the problem or choose one from your phone.",
        s2_title: "Tell us where",
        s2_text: "Type the address or street, or enter the coordinates.",
        s3_title: "Choose the category",
        s3_text: "Pick the type of problem: road hole, trash, water leak…",
        s4_title: "Add details (optional)",
        s4_text: "Tick the option and write a few words if you want to.",
        s5_title: "Send your report",
        s5_text: "Your report goes to the department in charge of that category.",
        cta: "Start a report"
      },
      fr: {
        page_title: "FIXMYCITY – Mode d'emploi",
        nav_report: "Signaler un problème",
        nav_all: "Tous les signalements",
        nav_reports: "Mes signalements",
        nav_info: "Mode d'emploi",
        lang_label: "Langue :",
        title: "Comment utiliser FIXMYCITY",
        subtitle: "Cinq étapes simples pour signaler un problème dans votre ville.",
        s1_title: "Ajoutez une photo",
        s1_text: "Prenez une photo du problème ou choisissez-en une sur votre téléphone.",
        s2_title: "Indiquez le lieu",
        s2_text: "Saisissez l'adresse ou la rue, ou entrez les coordonnées.",
        s3_title: "Choisissez la catégorie",
        s3_text: "Sélectionnez le type de problème : trou, déchets, fuite d'eau…",
        s4_title: "Ajoutez des détails (facultatif)",
        s4_text: "Cochez l'option et écrivez quelques mots si vous le souhaitez.",
        s5_title: "Envoyez le signalement",
        s5_text: "Votre signalement est transmis au service responsable de cette catégorie.",
        cta: "Commencer un signalement"
      },
      ar: {
        page_title: "FIXMYCITY – كيفية الاستخدام",
        nav_report: "الإبلاغ عن مشكلة",
        nav_all: "كل البلاغات",
        nav_reports: "بلاغاتي",
        nav_info: "كيفية الاستخدام",
        lang_label: "اللغة:",
        title: "كيف تستخدم FIXMYCITY",
        subtitle: "خمس خطوات بسيطة للإبلاغ عن مشكلة في مدينتك.",
        s1_title: "أضف صورة",
        s1_text: "التقط صورة للمشكلة أو اختر صورة من هاتفك.",
        s2_title: "حدد المكان",
        s2_text: "اكتب العنوان أو اسم الشارع، أو أدخل الإحداثيات.",
        s3_title: "اختر الفئة",
        s3_text: "اختر نوع المشكلة: حفرة في الطريق، قمامة، تسرب مياه…",
        s4_title: "أضف تفاصيل (اختياري)",
        s4_text: "فعّل الخيار واكتب بعض الكلمات إن أردت.",
        s5_title: "أرسل البلاغ",
        s5_text: "يصل بلاغك إلى القسم المسؤول عن هذه الفئة.",
        cta: "ابدأ بلاغًا"
      }
    };

    const select = document.getElementById("lang-select");

    function setLanguage(lang) {
      const dict = translations[lang] || translations.en;

      document.documentElement.lang = lang;
      document.documentElement.dir = lang === "ar" ? "rtl" : "ltr";
      document.title = dict.page_title;

      document.querySelectorAll("[data-i18n]").forEach((el) => {
        const key = el.getAttribute("data-i18n");
        if (dict[key]) el.textContent = dict[key];
      });

      localStorage.setItem("lang", lang);
    }

    select.addEventListener("change", (e) => setLanguage(e.target.value));

    // Load the saved language on page load
    const savedLang = localStorage.getItem("lang") || "en";
    select.value = savedLang;
    setLanguage(savedLang);
