<?php

return [
    'indexable' => env('SITE_INDEXABLE', env('APP_ENV', 'local') === 'production'),
    'google_site_verification' => env('GOOGLE_SITE_VERIFICATION'),
    'enquiries_enabled' => env('ENQUIRIES_ENABLED', false),
    'enquiries_to' => env('ENQUIRIES_TO', 'idealwoman764@gmail.com'),
    'social_links' => [
        'facebook' => env('SOCIAL_FACEBOOK_URL', 'https://www.facebook.com/share/19hy141uyo/?mibextid=wwXIfr'),
        'instagram' => env('SOCIAL_INSTAGRAM_URL', 'https://www.instagram.com/idealwomanuganda?cplk=N3oxbWc4cWtpamFt&utm_source=qr'),
        'threads' => env('SOCIAL_THREADS_URL', 'https://www.threads.com/@idealwomanuganda?igshid=NTc4MTIwNjQ2YQ=='),
        'x' => env('SOCIAL_X_URL', 'https://x.com/idealwomanugan1?s=11'),
        'linkedin' => env('SOCIAL_LINKEDIN_URL'),
        'youtube' => env('SOCIAL_YOUTUBE_URL', 'https://youtube.com/@idealwomanuganda3682?si=4k1604PWuDC7ZdBQ'),
        'tiktok' => env('SOCIAL_TIKTOK_URL'),
    ],
    'work' => [
        'skills-livelihoods' => ['title' => 'Skills & Livelihoods', 'icon' => 'skills', 'intro' => 'Practical skills. Independent futures.', 'description' => 'Vocational learning, tailoring, agriculture, entrepreneurship, financial literacy and a culture of saving.', 'detail' => 'Our objectives connect practical learning with economic independence: from arts, crafts and tailoring to agriculture and enterprise. We aim to strengthen financial management, savings schemes and informed decisions about working capital and investment.', 'image' => 'tailoring', 'alt' => 'Participants working together with fabric beside a sewing machine.'],
        'education-digital-skills' => ['title' => 'Education & Digital Skills', 'icon' => 'education', 'intro' => 'Learning that opens doors.', 'description' => 'Girl-child education, career guidance, computing skills and internship development.', 'detail' => 'We promote girl-child education and career guidance, alongside ICT and computing skills suited to a changing information era. Internship development and collaboration with individuals and institutions are part of these objectives.', 'image' => 'workshop', 'alt' => 'A facilitator speaking with participants at a workshop with laptops and notebooks.'],
        'health-wellbeing' => ['title' => 'Health & Wellbeing', 'icon' => 'health', 'intro' => 'Knowledge. Confidence. Wellbeing.', 'description' => 'Health education, HIV awareness, sexual and reproductive health and rights, guidance and counselling.', 'detail' => 'Our objectives include health education, HIV prevention and awareness, sexual and reproductive health and rights (SRHR), guidance and counselling. Contact the team to discuss current activities. This website does not provide medical advice or book clinical care.', 'image' => 'health', 'alt' => 'A woman in a Rotary vest standing beside a table of medical supplies at a pharmacy station.'],
        'leadership-rights' => ['title' => 'Leadership & Rights', 'icon' => 'leadership', 'intro' => 'Every voice has possibility.', 'description' => 'Self-discovery, leadership, women\'s rights, talent, sports and cultural participation.', 'detail' => 'We aim to support self-discovery, life-choice skills, leadership and talent development. Our objectives also encompass women\'s rights and access to legal support, inclusion, gender equality, sports and cultural heritage. Legal representation is not offered or booked through this website.', 'image' => 'dialogue', 'alt' => 'Participants and facilitators seated together during a group discussion.'],
        'community-environment' => ['title' => 'Community & Environment', 'icon' => 'community', 'intro' => 'Stronger communities, together.', 'description' => 'Volunteering, community engagement, clean-ups, tree planting, recycling and environmental protection.', 'detail' => 'Our community objectives bring people together through volunteering and outreach. Environmental priorities include waste collection, tree planting, plastic recycling, wetland protection and responsible management of shared resources.', 'image' => null],
        'women-empowerment' => ['title' => 'Women Empowerment', 'icon' => 'leadership', 'intro' => 'Confidence, voice and opportunity.', 'description' => 'Supporting women to build confidence, develop skills and participate in community life.', 'detail' => 'Women empowerment connects our work in skills, education, leadership and rights. We support women and girls to make informed choices, build independence and contribute to their communities.', 'image' => 'women-empowerment', 'alt' => 'Women gathered outdoors making beadwork and crafts together.'],
        'climate-change' => ['title' => 'Climate Change', 'icon' => 'community', 'intro' => 'Building climate-aware communities.', 'description' => 'Climate awareness, environmental protection and community participation.', 'detail' => 'Climate change is part of our work with women, girls and communities. We connect environmental awareness with community participation and care for shared resources.', 'image' => 'climate-briquette-workshop', 'alt' => 'Community members taking part in a briquette-making workshop outdoors.'],
    ],
    'objectives' => [
        'skills-livelihoods' => [
            'Entrepreneurship skills, innovation and wealth creation among women and the wider public.',
            'Financial self-sufficiency through agriculture, arts and crafts, tailoring, manufacturing and public service enterprise.',
            'SACCO development, a savings culture, and financial literacy for investment, working capital, financing and profit allocation.',
        ],
        'education-digital-skills' => [
            'Girl-child education, career guidance and counselling in collaboration with individuals and organisations.',
            'ICT and computing skills suited to a fast-moving information era.',
            'Internship development and placements as part of wider learning and collaboration objectives.',
            'Education support, sponsorship for disadvantaged families and school infrastructure as priorities described in the profile.',
        ],
        'health-wellbeing' => [
            'Health education, HIV prevention and awareness, and sexual and reproductive health and rights (SRHR).',
            'Guidance, counselling and health service delivery as organisational objectives, subject to current capacity and appropriate collaboration.',
        ],
        'leadership-rights' => [
            'Self-discovery, talent development, leadership, management and informed life choices.',
            'Inclusion, gender equality and equity among women, girls and youth.',
            'Women\'s human rights and access to legal support on business, economic, social and family matters.',
            'Participation in sports, games, aerobics, talent search, beauty pageants, fashion, modelling and awards.',
            'Cultural heritage and a strong values foundation in support of women\'s emancipation.',
        ],
        'community-environment' => [
            'Publications, magazines, events and volunteering that encourage community engagement.',
            'Waste collection, community clean-ups, tree planting and plastic recycling.',
            'Environmental protection, wetland protection and sound management of shared community resources.',
            'Corporate social responsibility, sponsorship and support for sick and vulnerable community members.',
        ],
    ],
    'stories' => [
        'students-championing-peace' => ['title' => 'Students Championing Peace', 'category' => 'Community dialogue', 'image' => 'peace', 'alt' => 'Participants seated at wooden classroom desks during a learning session.', 'excerpt' => 'Creating spaces for learning, dialogue and peaceful communities.', 'paragraphs' => [
            'Students Championing Peace is an awareness initiative documented in Ideal Woman\'s activity photographs. The event banner describes awareness creation on violent extremism and its prevention in secondary and tertiary institutions in Masaka City.',
            'The photographs show classroom learning, group discussions and participants coming together around the theme of peace. These spaces for learning and dialogue reflect the importance of community participation.',
            'To learn more about the initiative or explore collaboration on community learning, contact the Ideal Woman team.',
        ]],
        'learning-through-making' => ['title' => 'Learning through making', 'category' => 'Skills in practice', 'image' => 'tailoring', 'alt' => 'People sharing sewing techniques beside a sewing machine.', 'excerpt' => 'A photographic look at sharing practical sewing skills.', 'paragraphs' => [
            'The activity photographs document people working together with fabric, needles and sewing machines. They offer a glimpse of practical learning and the exchange of sewing techniques.',
            'Vocational skills and tailoring sit within Ideal Woman\'s wider objectives for financial self-sufficiency, entrepreneurship and independent futures.',
            'Contact the team to discuss skills collaboration or enquire about current activities.',
        ]],
    ],
];
