export const languages = ['en', 'lg', 'sw', 'fr', 'ar', 'es', 'pt', 'zh-CN'];

// Each row contains the English source followed by lg, sw, fr, ar, es, pt, zh-CN.
const phrases = [
    ['Close accessibility settings', 'Ggalawo enteekateeka z’obwangu bw’okukozesa', 'Funga mipangilio ya ufikivu', 'Fermer les paramètres d’accessibilité', 'إغلاق إعدادات إمكانية الوصول', 'Cerrar ajustes de accesibilidad', 'Fechar configurações de acessibilidade', '关闭无障碍设置'],
    ['Saved automatically on this device.', 'Biterekebwa byokka ku kyuma kino.', 'Inahifadhiwa kiotomatiki kwenye kifaa hiki.', 'Enregistré automatiquement sur cet appareil.', 'تُحفظ تلقائيًا على هذا الجهاز.', 'Se guarda automáticamente en este dispositivo.', 'Salvo automaticamente neste dispositivo.', '自动保存在此设备上。'],
    ['Choose your language', 'Londa olulimi lwo', 'Chagua lugha yako', 'Choisissez votre langue', 'اختر لغتك', 'Elige tu idioma', 'Escolha seu idioma', '选择您的语言'],
    ['Languages', 'Ennimi', 'Lugha', 'Langues', 'اللغات', 'Idiomas', 'Idiomas', '语言'],
    ['Built-in translations. Some programme articles remain in English.', 'Enzivuunula ziri ku mukutu. Ebimu ku biwandiiko by’enteekateeka bikyali mu Lungereza.', 'Tafsiri zimejumuishwa. Baadhi ya makala za programu bado ziko kwa Kiingereza.', 'Traductions intégrées. Certains articles restent en anglais.', 'ترجمات مدمجة. تبقى بعض مقالات البرامج باللغة الإنجليزية.', 'Traducciones integradas. Algunos artículos siguen en inglés.', 'Traduções integradas. Alguns artigos permanecem em inglês.', '内置翻译。部分项目文章仍为英文。'],
    ['All fields are required unless marked optional.', 'Jjuza ebifo byonna okuggyako ebirambiddwa nti si bya buwaze.', 'Sehemu zote zinahitajika isipokuwa zilizoandikwa si lazima.', 'Tous les champs sont obligatoires, sauf indication contraire.', 'جميع الحقول مطلوبة ما لم يُذكر أنها اختيارية.', 'Todos los campos son obligatorios salvo los marcados como opcionales.', 'Todos os campos são obrigatórios, exceto os marcados como opcionais.', '除标注为选填的项目外，所有字段均为必填。'],
    ['Please check your enquiry.', 'Kebera obubaka bwo.', 'Tafadhali kagua ujumbe wako.', 'Veuillez vérifier votre demande.', 'يرجى مراجعة استفسارك.', 'Revisa tu consulta.', 'Verifique sua consulta.', '请检查您的咨询内容。'],
    ['Home', 'Awaka', 'Mwanzo', 'Accueil', 'الرئيسية', 'Inicio', 'Início', '首页'],
    ['About Us', 'Ebitukwatako', 'Kuhusu sisi', 'À propos', 'من نحن', 'Quiénes somos', 'Sobre nós', '关于我们'],
    ['Our Work', 'Emirimu gyaffe', 'Kazi zetu', 'Nos actions', 'عملنا', 'Nuestro trabajo', 'Nosso trabalho', '我们的工作'],
    ['Stories', 'Emboozi', 'Habari', 'Actualités', 'قصص', 'Historias', 'Histórias', '故事'],
    ['Stories & Updates', 'Emboozi n’amawulire', 'Habari na taarifa', 'Actualités et témoignages', 'قصص ومستجدات', 'Historias y novedades', 'Histórias e novidades', '故事与动态'],
    ['Get Involved', 'Weetabe', 'Shiriki', 'Participer', 'شارك معنا', 'Participa', 'Participe', '参与我们'],
    ['Support Us', 'Tuwagire', 'Tuunge mkono', 'Nous soutenir', 'ادعمنا', 'Apóyanos', 'Apoie-nos', '支持我们'],
    ['Partner With Us', 'Kola naffe', 'Shirikiana nasi', 'Devenir partenaire', 'كن شريكًا لنا', 'Colabora con nosotros', 'Seja nosso parceiro', '成为合作伙伴'],
    ['Volunteer', 'Nnakyewa', 'Jitolee', 'Bénévolat', 'تطوع', 'Voluntariado', 'Voluntariado', '志愿服务'],
    ['Volunteer With Us', 'Kola naffe nga nnakyewa', 'Jitolee pamoja nasi', 'Devenir bénévole', 'تطوع معنا', 'Haz voluntariado con nosotros', 'Seja voluntário conosco', '加入志愿服务'],
    ['Contact', 'Tuukirira', 'Wasiliana', 'Contact', 'اتصل بنا', 'Contacto', 'Contato', '联系我们'],
    ['Contact Us', 'Tuukirira', 'Wasiliana nasi', 'Nous contacter', 'اتصل بنا', 'Contáctanos', 'Entre em contato', '联系我们'],
    ['Menu', 'Menyu', 'Menyu', 'Menu', 'القائمة', 'Menú', 'Menu', '菜单'],
    ['Skip to content', 'Genda ku biri ku lupapula', 'Ruka hadi maudhui', 'Aller au contenu', 'انتقل إلى المحتوى', 'Ir al contenido', 'Ir para o conteúdo', '跳转到正文'],
    ['Translate', 'Vvuunula', 'Tafsiri', 'Traduire', 'ترجمة', 'Traducir', 'Traduzir', '翻译'],
    ['Accessibility', 'Obwangu bw’okukozesa', 'Ufikivu', 'Accessibilité', 'إمكانية الوصول', 'Accesibilidad', 'Acessibilidade', '无障碍'],
    ['Tell us how you would like to help', 'Tubuulire engeri gy’oyagala okuyambamu', 'Tuambie ungependa kusaidia vipi', 'Dites-nous comment vous souhaitez aider', 'أخبرنا كيف ترغب في المساعدة', 'Cuéntanos cómo te gustaría ayudar', 'Conte como gostaria de ajudar', '请告诉我们您希望如何提供帮助'],
    ['Share your details and how you would like to support our work. Our team will get in touch.', 'Tuwa ebikukwatako n’engeri gy’oyagala okuwagiramu emirimu gyaffe. Tujja kukutuukirira.', 'Tupe maelezo yako na jinsi ungependa kuunga mkono kazi zetu. Timu yetu itawasiliana nawe.', 'Indiquez vos coordonnées et comment vous souhaitez soutenir nos actions. Notre équipe vous contactera.', 'شارك بياناتك وكيف ترغب في دعم عملنا. سيتواصل معك فريقنا.', 'Comparte tus datos y cómo te gustaría apoyar nuestro trabajo. Nuestro equipo se pondrá en contacto contigo.', 'Compartilhe seus dados e como gostaria de apoiar nosso trabalho. Nossa equipe entrará em contato.', '请留下您的联系方式，并说明您希望如何支持我们的工作。我们的团队会与您联系。'],
    ['Tell us about yourself', 'Tubuulire ebikukwatako', 'Tuambie kuhusu wewe', 'Parlez-nous de vous', 'أخبرنا عن نفسك', 'Háblanos de ti', 'Conte sobre você', '请介绍一下自己'],
    ['Share your skills, interests and availability, and tell us how you would like to help. Our team will get in touch to discuss volunteering opportunities.', 'Tubuulire obukugu bwo, by’oyagala n’obudde bw’olina, n’engeri gy’oyagala okuyambamu. Tujja kukutuukirira twogere ku mirimu gya bannakyewa.', 'Tuambie ujuzi wako, mambo unayopenda na muda ulio nao, na jinsi ungependa kusaidia. Timu yetu itawasiliana nawe kuhusu fursa za kujitolea.', 'Présentez vos compétences, vos centres d’intérêt et vos disponibilités. Notre équipe vous contactera pour discuter des possibilités de bénévolat.', 'شارك مهاراتك واهتماماتك وأوقات تفرغك وكيف ترغب في المساعدة. سيتواصل فريقنا معك لمناقشة فرص التطوع.', 'Comparte tus habilidades, intereses y disponibilidad, y cómo te gustaría ayudar. Nuestro equipo te contactará para hablar de oportunidades de voluntariado.', 'Compartilhe suas habilidades, interesses e disponibilidade e como gostaria de ajudar. Nossa equipe entrará em contato para conversar sobre oportunidades de voluntariado.', '请介绍您的技能、兴趣、可参与时间以及希望提供的帮助。我们的团队会与您讨论志愿服务机会。'],
    ['Name', 'Erinnya', 'Jina', 'Nom', 'الاسم', 'Nombre', 'Nome', '姓名'],
    ['Email', 'Emeyiro', 'Barua pepe', 'Adresse e-mail', 'البريد الإلكتروني', 'Correo electrónico', 'E-mail', '电子邮箱'],
    ['Organisation', 'Ekibiina', 'Shirika', 'Organisation', 'المؤسسة', 'Organización', 'Organização', '机构'],
    ['(optional)', '(si kya buwaze)', '(si lazima)', '(facultatif)', '(اختياري)', '(opcional)', '(opcional)', '（选填）'],
    ['Enquiry type', 'Ekika ky’obubaka', 'Aina ya ujumbe', 'Type de demande', 'نوع الاستفسار', 'Tipo de consulta', 'Tipo de consulta', '咨询类型'],
    ['Choose an enquiry type', 'Londa ekika ky’obubaka', 'Chagua aina ya ujumbe', 'Choisissez un type de demande', 'اختر نوع الاستفسار', 'Elige un tipo de consulta', 'Escolha o tipo de consulta', '请选择咨询类型'],
    ['Partnership', 'Enkolagana', 'Ushirikiano', 'Partenariat', 'شراكة', 'Colaboración', 'Parceria', '合作'],
    ['Training & mentorship', 'Okutendekebwa n’okulungamizibwa', 'Mafunzo na ushauri', 'Formation et mentorat', 'تدريب وإرشاد', 'Formación y mentoría', 'Formação e mentoria', '培训与指导'],
    ['Equipment & programme support', 'Ebyuma n’okuwagira enteekateeka', 'Vifaa na msaada wa programu', 'Équipement et soutien aux programmes', 'معدات ودعم البرامج', 'Equipos y apoyo a programas', 'Equipamentos e apoio a programas', '设备与项目支持'],
    ['Market access', 'Okufuna obutale', 'Upatikanaji wa masoko', 'Accès aux marchés', 'الوصول إلى الأسواق', 'Acceso a mercados', 'Acesso a mercados', '市场渠道'],
    ['Volunteering', 'Obwannakyewa', 'Kujitolea', 'Bénévolat', 'التطوع', 'Voluntariado', 'Voluntariado', '志愿服务'],
    ['General enquiry', 'Okubuuza okwa bulijjo', 'Swali la jumla', 'Demande générale', 'استفسار عام', 'Consulta general', 'Consulta geral', '一般咨询'],
    ['Message', 'Obubaka', 'Ujumbe', 'Message', 'الرسالة', 'Mensaje', 'Mensagem', '留言'],
    ['Tell us how you would like to connect. Please do not include sensitive medical or personal information.', 'Tubuulire engeri gy’oyagala okukolaganamu naffe. Tossaamu bya bulamu oba ebikukwatako eby’ekyama.', 'Tuambie ungependa kuwasiliana nasi vipi. Tafadhali usiweke taarifa nyeti za afya au za binafsi.', 'Dites-nous comment vous souhaitez collaborer. Ne communiquez pas de données médicales ou personnelles sensibles.', 'أخبرنا كيف ترغب في التواصل. يرجى عدم تضمين معلومات طبية أو شخصية حساسة.', 'Cuéntanos cómo te gustaría colaborar. No incluyas información médica o personal sensible.', 'Conte como gostaria de colaborar. Não inclua informações médicas ou pessoais sensíveis.', '请告诉我们您希望如何合作。请勿填写敏感的医疗或个人信息。'],
    ['Send support enquiry', 'Weereza obubaka bw’obuwagizi', 'Tuma ujumbe wa msaada', 'Envoyer la demande de soutien', 'أرسل طلب الدعم', 'Enviar consulta de apoyo', 'Enviar consulta de apoio', '发送支持咨询'],
    ['Send volunteer enquiry', 'Weereza obubaka bw’obwannakyewa', 'Tuma ujumbe wa kujitolea', 'Envoyer la demande de bénévolat', 'أرسل طلب التطوع', 'Enviar consulta de voluntariado', 'Enviar consulta de voluntariado', '发送志愿服务咨询'],
    ['Send an enquiry', 'Weereza obubaka', 'Tuma ujumbe', 'Envoyer une demande', 'أرسل استفسارًا', 'Enviar una consulta', 'Enviar uma consulta', '发送咨询'],
    ['Send enquiry', 'Weereza obubaka', 'Tuma ujumbe', 'Envoyer la demande', 'إرسال الاستفسار', 'Enviar consulta', 'Enviar consulta', '发送咨询'],
    ['Online sending is currently unavailable.', 'Okuweereza ku mutimbagano tekusoboka mu kiseera kino.', 'Kutuma mtandaoni hakupatikani kwa sasa.', 'L’envoi en ligne est actuellement indisponible.', 'الإرسال عبر الإنترنت غير متاح حاليًا.', 'El envío en línea no está disponible actualmente.', 'O envio on-line está indisponível no momento.', '在线发送暂不可用。'],
    ['Please', 'Tukusaba', 'Tafadhali', 'Veuillez', 'يرجى', 'Por favor,', 'Por favor,', '请'],
    ['email us directly', 'tuweereze emeyiro butereevu', 'tutumie barua pepe moja kwa moja', 'nous écrire directement par e-mail', 'راسلنا مباشرة عبر البريد الإلكتروني', 'envíanos un correo directamente', 'envie-nos um e-mail diretamente', '直接给我们发送电子邮件'],
    ['or call', 'oba kuba ku', 'au piga simu', 'ou appelez le', 'أو اتصل على', 'o llama al', 'ou ligue para', '或致电'],
    ['We use your details to respond to your enquiry. Successfully sent enquiries are emailed to the Ideal Woman team and saved for follow-up by authorised staff.', 'Tukozesa ebikukwatako okuddamu obubaka bwo. Obubaka obuweerezeddwa bulungi buweerezebwa eri ttiimu ya Ideal Woman ku emeyiro era buterekebwa abakozi abakkirizibwa okubugoberera.', 'Tunatumia taarifa zako kujibu ujumbe wako. Ujumbe uliotumwa kwa mafanikio hupelekwa kwa timu ya Ideal Woman kwa barua pepe na kuhifadhiwa kwa ufuatiliaji na wafanyakazi walioidhinishwa.', 'Nous utilisons vos coordonn?es pour r?pondre ? votre demande. Les demandes envoy?es sont transmises par e-mail ? l??quipe Ideal Woman et conserv?es pour le suivi par le personnel autoris?.', '?????? ??????? ???? ??? ????????. ????? ??????????? ??????? ????? ??? ???? Ideal Woman ??????? ?????????? ?????? ???????? ?????? ???????? ????????.', 'Usamos tus datos para responder a tu consulta. Las consultas enviadas se remiten por correo al equipo de Ideal Woman y se guardan para su seguimiento por personal autorizado.', 'Usamos seus dados para responder ? consulta. As consultas enviadas s?o encaminhadas por e-mail ? equipe Ideal Woman e salvas para acompanhamento por funcion?rios autorizados.', '?????????????????????????????? Ideal Woman ?????????????????'],
    ['Explore', 'Laba ebisingawo', 'Gundua', 'Découvrir', 'استكشف', 'Explora', 'Explore', '探索'],
    ['Visit us', 'Tukyalire', 'Tutembelee', 'Nous rendre visite', 'زرنا', 'Visítanos', 'Visite-nos', '来访'],
    ['Search for our office on Maps', 'Noonya ofiisi yaffe ku maapu', 'Tafuta ofisi yetu kwenye ramani', 'Trouver notre bureau sur la carte', 'ابحث عن مكتبنا على الخريطة', 'Busca nuestra oficina en el mapa', 'Encontre nosso escritório no mapa', '在地图上查找我们的办公室'],
    ['Start a conversation', 'Tandika okwogera naffe', 'Anza mazungumzo', 'Parlons ensemble', 'ابدأ محادثة', 'Inicia una conversación', 'Inicie uma conversa', '开始交流'],
    ['Download Our Profile (PDF)', 'Wanula ebikwata ku kibiina (PDF)', 'Pakua wasifu wetu (PDF)', 'Télécharger notre présentation (PDF)', 'حمّل ملفنا التعريفي (PDF)', 'Descarga nuestra presentación (PDF)', 'Baixe nossa apresentação (PDF)', '下载机构简介（PDF）'],
    ['Reaching out to you.', 'Tukutuukirira.', 'Tunawafikia.', 'À votre rencontre.', 'نتواصل معكم.', 'Acercándonos a ti.', 'Chegando até você.', '与您携手。'],
    ['Building skills, confidence and possibility', 'Tuzimba obukugu, obwesige n’emikisa', 'Kujenga ujuzi, kujiamini na fursa', 'Développer les compétences, la confiance et les possibilités', 'نبني المهارات والثقة والفرص', 'Desarrollando habilidades, confianza y oportunidades', 'Desenvolvendo habilidades, confiança e oportunidades', '培养技能、自信与机遇'],
    ['across Greater Masaka, Uganda.', 'mu Greater Masaka yonna, Uganda.', 'katika eneo la Greater Masaka, Uganda.', 'dans le Grand Masaka, en Ouganda.', 'في منطقة ماساكا الكبرى، أوغندا.', 'en la región de Masaka, Uganda.', 'na região de Masaka, Uganda.', '遍及乌干达大马萨卡地区。'],
    ['Text size', 'Obunene bw’ennukuta', 'Ukubwa wa maandishi', 'Taille du texte', 'حجم النص', 'Tamaño del texto', 'Tamanho do texto', '文字大小'],
    ['Standard', 'Obwa bulijjo', 'Kawaida', 'Normale', 'عادي', 'Normal', 'Normal', '标准'],
    ['Larger', 'Ennene', 'Kubwa', 'Grande', 'أكبر', 'Grande', 'Grande', '较大'],
    ['Extra large', 'Ennene ennyo', 'Kubwa zaidi', 'Très grande', 'كبير جدًا', 'Muy grande', 'Muito grande', '特大'],
    ['Largest', 'Ezisinga obunene', 'Kubwa kabisa', 'Maximale', 'الأكبر', 'Máximo', 'Máximo', '最大'],
    ['Higher contrast', 'Enjawulo ya langi esingako', 'Utofauti mkubwa wa rangi', 'Contraste renforcé', 'تباين أعلى', 'Mayor contraste', 'Maior contraste', '高对比度'],
    ['Color theme', 'Langi z’olupapula', 'Mandhari ya rangi', 'Thème de couleurs', 'نمط الألوان', 'Tema de color', 'Tema de cores', '颜色主题'],
    ['Original colors', 'Langi ezasooka', 'Rangi asili', 'Couleurs d’origine', 'الألوان الأصلية', 'Colores originales', 'Cores originais', '原始颜色'],
    ['Warm cream', 'Langi ya kirimu', 'Krimu yenye joto', 'Crème chaude', 'كريمي دافئ', 'Crema cálido', 'Creme quente', '暖米色'],
    ['Cool blue', 'Bbululu ennyogovu', 'Bluu tulivu', 'Bleu doux', 'أزرق هادئ', 'Azul suave', 'Azul suave', '冷蓝色'],
    ['Dark', 'Enzibu', 'Giza', 'Sombre', 'داكن', 'Oscuro', 'Escuro', '深色'],
    ['Higher contrast overrides the selected color theme.', 'Enjawulo ya langi esingako ekozesa langi zaayo mu kifo ky’ezo z’olonze.', 'Utofauti mkubwa wa rangi unatangulia mandhari uliyochagua.', 'Le contraste renforcé remplace le thème choisi.', 'التباين الأعلى يتجاوز نمط الألوان المحدد.', 'El mayor contraste sustituye el tema elegido.', 'O maior contraste substitui o tema escolhido.', '高对比度会覆盖所选颜色主题。'],
    ['Underline links', 'Teeka olunyiriri wansi w’enyunzi', 'Pigia viungo mstari', 'Souligner les liens', 'تسطير الروابط', 'Subrayar enlaces', 'Sublinhar links', '链接加下划线'],
    ['More reading space', 'Okwongera amabanga mu kusoma', 'Nafasi zaidi ya kusoma', 'Espacement de lecture accru', 'تباعد أكبر للقراءة', 'Más espacio de lectura', 'Mais espaço para leitura', '增大阅读间距'],
    ['Reduce motion and pause slideshow', 'Kendeeza okutambula era oyimirize ebifaananyi', 'Punguza mwendo na simamisha picha', 'Réduire les animations et suspendre le diaporama', 'تقليل الحركة وإيقاف عرض الصور', 'Reducir movimiento y pausar diapositivas', 'Reduzir movimento e pausar apresentação', '减少动态效果并暂停幻灯片'],
    ['Reset preferences', 'Zzaayo enteekateeka', 'Rejesha mipangilio', 'Réinitialiser les préférences', 'إعادة ضبط التفضيلات', 'Restablecer preferencias', 'Redefinir preferências', '重置偏好'],
    ['Preferences saved.', 'Enteekateeka ziterekeddwa.', 'Mipangilio imehifadhiwa.', 'Préférences enregistrées.', 'تم حفظ التفضيلات.', 'Preferencias guardadas.', 'Preferências salvas.', '偏好已保存。'],
    ['Her potential.', 'Obusobozi bwe.', 'Uwezo wake.', 'Son potentiel.', 'إمكاناتها.', 'Su potencial.', 'Seu potencial.', '她的潜能。'],
    ['Her choices.', 'Okusalawo kwe.', 'Chaguo zake.', 'Ses choix.', 'خياراتها.', 'Sus decisiones.', 'Suas escolhas.', '她的选择。'],
    ['Her future.', 'Ebiseera bye eby’omu maaso.', 'Mustakabali wake.', 'Son avenir.', 'مستقبلها.', 'Su futuro.', 'Seu futuro.', '她的未来。'],
    ['Learn more', 'Manya ebisingawo', 'Jifunze zaidi', 'En savoir plus', 'اعرف المزيد', 'Más información', 'Saiba mais', '了解更多'],
];

export const dictionaries = Object.fromEntries(languages.slice(1).map((language, index) => [
    language, Object.fromEntries(phrases.map((row) => [row[0], row[index + 1]])),
]));

for (const dictionary of Object.values(dictionaries)) {
    dictionary['Online sending is currently unavailable. Please'] = `${dictionary['Online sending is currently unavailable.']} ${dictionary.Please}`;
}

export function translateText(source, language) {
    const key = source.trim().replace(/\s+/g, ' ');
    const translated = dictionaries[language]?.[key];
    if (!translated) return source;
    return source.replace(source.trim(), translated);
}

export function textLanguage(source, language) {
    const key = source.trim().replace(/\s+/g, ' ');
    return dictionaries[language]?.[key] ? language : 'en';
}
