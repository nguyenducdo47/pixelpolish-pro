<?php

/**
 * CV overlay: tay ngang (QTKD – tin học); kinh nghiệm kinh doanh online + tiếp xúc kho vận/WMS ở mức cơ bản; lập trình hỗ trợ số hóa.
 */
return [
    'skip_legacy_skill_categories' => true,
    'replace_legacy_projects' => true,
    'skip_legacy_principles' => true,

    'profile' => [
        'headline' => [
            'vi' => 'LẬP TRÌNH VIÊN HỆ THỐNG WEBSITE',
            'en' => 'WEB DEVELOPER SYSTEM',
        ],
        'tagline' => [
            'vi' => 'Quản trị – tin học; tham gia triển khai hệ thống TMĐT & Affiliate (Tập đoàn Liên Hoa) và kinh doanh online. Hỗ trợ nâng cấp phần mềm quản lý, hướng tới số hóa quy trình kinh doanh và vận hành.',
            'en' => 'Business and IT background; Lien Hoa Group e-commerce/affiliate rollout and online sales. Supports system upgrades and management software—focused on digitizing business and operations processes.',
        ],
        'about' => [
            'vi' => '<p>Tham gia <strong>triển khai và nâng cấp hệ thống</strong> phần mềm <strong>quản lý</strong> kho, đơn hàng: ghi nhận yêu cầu từ người dùng, hỗ trợ kiểm thử và chỉnh sửa chức năng — ưu tiên chuyển quy trình <strong>kinh doanh</strong>/vận hành thành giải pháp <strong>số hóa</strong> trên hệ thống.</p>',
            'en' => '<p>Contributing to <strong>system rollout and upgrades</strong> for warehouse and order <strong>management</strong> software—capturing user needs, testing, and fixes; turning <strong>business</strong> and operations flows into <strong>digitized</strong> system solutions.</p>',
        ],
        'philosophy_quote' => [
            'vi' => 'Kinh nghiệm bán hàng giúp hiểu khách hàng; học kho và lập trình giúp làm việc bền vững hơn.',
            'en' => 'Selling teaches the customer; learning warehouse work and code makes the rest sustainable.',
        ],
    ],
    'cv_settings' => [
        'resume_summary_title' => [
            'vi' => 'TÓM TẮT SƠ YẾU LÝ LỊCH',
            'en' => 'RESUME SUMMARY',
        ],
        'resume_summary' => null,
        'show_resume_summary' => true,
        'show_principles' => false,
        'show_certifications' => true,
        'show_career_targets' => true,
        'show_strengths' => true,
        'show_work_experience' => true,
    ],
    'work_experiences' => [
        [
            'company' => [
                'vi' => 'Công ty Cổ phần Tư vấn và Cung cấp Giải pháp Quản trị Chuẩn Việt Vang',
                'en' => 'Viet Vang Standards Management Consulting and Solutions JSC',
            ],
            'position' => [
                'vi' => 'Lập trình viên website',
                'en' => 'Web developer',
            ],
            'period' => '2024 – 2026',
            'details' => [
                'vi' => '<p>Tham gia <strong>triển khai, nâng cấp và bảo trì hệ thống</strong> website cùng module <strong>quản lý</strong> theo dự án khách hàng; hỗ trợ <strong>số hóa</strong> luồng <strong>kinh doanh</strong> và vận hành.</p>',
                'en' => '<p>Helped <strong>roll out, upgrade, and maintain systems</strong> and <strong>management</strong> modules for clients; supported <strong>digitizing</strong> <strong>business</strong> and operations.</p>',
            ],
        ],
    ],
    'skill_categories' => [
        [
            'name' => ['vi' => 'Kinh doanh online & affiliate', 'en' => 'Online sales & affiliate'],
            'sort_order' => 0,
            'skills' => [
                [
                    'name' => 'TMĐT & Affiliate (hệ thống)',
                    'description' => [
                        'vi' => 'Triển khai hệ thống bán hàng online & affiliate; quản lý chính sách hoa hồng/chiến dịch theo mô hình kinh doanh',
                        'en' => 'E-commerce/affiliate system rollout; campaign and commission management aligned with business model',
                    ],
                    'level' => 68,
                ],
                [
                    'name' => 'Đơn hàng & tồn quy mô nhỏ',
                    'description' => [
                        'vi' => 'Quản lý đơn – tồn quy mô nhỏ; số hóa dần theo dõi kinh doanh trên Excel trước khi lên hệ thống',
                        'en' => 'Small-scale order/stock management; gradual digitization of sales tracking before full systems',
                    ],
                    'level' => 65,
                ],
                [
                    'name' => 'Nội dung & chăm sóc khách',
                    'description' => [
                        'vi' => 'Mô tả sản phẩm, tư vấn đơn giản, xử lý phản hồi và đổi trả cơ bản',
                        'en' => 'Product copy, basic consulting, feedback and simple returns',
                    ],
                    'level' => 66,
                ],
            ],
        ],
        [
            'name' => ['vi' => 'Kho vận (đang học)', 'en' => 'Warehouse (learning)'],
            'sort_order' => 1,
            'skills' => [
                [
                    'name' => 'Nhập – xuất – tồn cơ bản',
                    'description' => [
                        'vi' => 'Hỗ trợ nhận hàng, soạn – đóng gói, cập nhật tồn theo hướng dẫn',
                        'en' => 'Assisted receiving, pick-pack, stock updates with guidance',
                    ],
                    'level' => 58,
                ],
                [
                    'name' => 'Kiểm kê & đối chiếu',
                    'description' => [
                        'vi' => 'Tham gia kiểm kê, báo chênh lệch, học quy trình đối chiếu số liệu',
                        'en' => 'Joined cycle counts, reported variances, learning reconciliation',
                    ],
                    'level' => 55,
                ],
            ],
        ],
        [
            'name' => ['vi' => 'Lập trình & số hóa quy trình', 'en' => 'Development & process digitization'],
            'sort_order' => 2,
            'skills' => [
                [
                    'name' => 'Nghiệp vụ → chức năng hệ thống',
                    'description' => [
                        'vi' => 'Chuyển nghiệp vụ kinh doanh/vận hành thành yêu cầu triển khai trên WMS và phần mềm quản lý',
                        'en' => 'Turn business/ops flows into requirements for WMS and management system rollout',
                    ],
                    'level' => 66,
                ],
                [
                    'name' => 'Nâng cấp & bảo trì phần mềm kho',
                    'description' => [
                        'vi' => 'Nâng cấp hệ thống quản lý kho: module nhập – xuất – tồn, tra cứu tồn và lịch sử giao dịch',
                        'en' => 'Upgrade warehouse management systems—inbound/outbound/inventory modules and transaction history',
                    ],
                    'level' => 68,
                ],
                [
                    'name' => 'Lập trình đa ngôn ngữ',
                    'description' => [
                        'vi' => 'Thông thạo nhiều ngôn ngữ lập trình; phát triển, bảo trì web và phần mềm quản lý — triển khai tính năng theo yêu cầu dự án và số hóa quy trình',
                        'en' => 'Proficient across multiple programming languages; builds and maintains web and management software—features and digitization per project needs',
                    ],
                    'level' => 72,
                ],
                [
                    'name' => 'Dữ liệu đơn hàng & báo cáo',
                    'description' => [
                        'vi' => 'Theo dõi đơn, đối soát doanh thu cơ bản; hỗ trợ báo cáo tồn/đơn trên hệ thống',
                        'en' => 'Order tracking, basic revenue reconciliation; stock/order reporting support',
                    ],
                    'level' => 62,
                ],
            ],
        ],
    ],
    'projects' => [
        [
            'title' => [
                'vi' => 'Nền tảng TMĐT & Affiliate – Tập đoàn Liên Hoa',
                'en' => 'E-commerce & Affiliate Platform – Lien Hoa Group',
            ],
            'subtitle' => [
                'vi' => 'Triển khai hệ thống TMĐT + Affiliate | Số hóa kênh kinh doanh',
                'en' => 'System rollout | Digitized sales channels',
            ],
            'period' => '11/2025 – 05/2026',
            'demo_url' => 'https://aloa.asia',
            'demo_label' => 'aloa.asia',
            'solution' => [
                'vi' => 'Tham gia <strong>triển khai và nâng cấp hệ thống</strong> <strong>thương mại điện tử</strong> (Bagisto) cho <strong>Tập đoàn Liên Hoa</strong>, <strong>mở rộng quản lý Affiliate</strong> — <strong>số hóa</strong> kênh <strong>kinh doanh</strong> với chính sách hoa hồng, chiến dịch và quy tắc affiliate riêng trên một nền tảng thống nhất.',
                'en' => 'Contributed to <strong>rolling out and upgrading</strong> <strong>Lien Hoa Group’s</strong> <strong>e-commerce system</strong> (Bagisto) and <strong>affiliate management</strong>—<strong>digitizing</strong> <strong>sales</strong> channels with custom commission and campaign rules on one platform.',
            ],
            'highlights' => [
                'vi' => [
                    'Triển khai chức năng Affiliate gắn luồng TMĐT — số hóa bán hàng và hoa hồng trên một hệ thống',
                    'Xây dựng logic tính hoa hồng và quản lý chiến dịch affiliate theo chính sách kinh doanh nội bộ',
                    'Cấu hình quy tắc affiliate (tỷ lệ, điều kiện, phạm vi sản phẩm) phù hợp mô hình phân phối của công ty',
                    'Phát triển API và module mở rộng; tối ưu xử lý dữ liệu và hiệu năng hệ thống',
                ],
                'en' => [
                    'Rolled out affiliate features on the e-commerce flow—digitized sales and commissions on one system',
                    'Built commission logic and campaign management under internal business policies',
                    'Configured affiliate rules (rates, conditions, product scope) for the company’s distribution model',
                    'Developed APIs and extension modules; improved data processing and performance',
                ],
            ],
            'tech_stack' => ['Laravel', 'Vue.js', 'MySQL'],
        ],
        [
            'title' => [
                'vi' => 'Kinh doanh bán cà phê online',
                'en' => 'Online coffee sales',
            ],
            'subtitle' => [
                'vi' => 'Kinh doanh online | Quản lý đơn – tồn',
                'en' => 'Online business | Order & stock management',
            ],
            'period' => '2023 – 2024',
            'solution' => [
                'vi' => 'Mô hình <strong>kinh doanh online</strong> cà phê: <strong>quản lý</strong> đơn – tồn, đóng gói và giao hàng — bước đầu <strong>số hóa</strong> theo dõi doanh thu và tồn trên Excel.',
                'en' => 'Online <strong>coffee business</strong>: small-batch <strong>order and stock management</strong>, packing and delivery—early <strong>digitization</strong> of sales and inventory in Excel.',
            ],
            'highlights' => [
                'vi' => [
                    'Xây dựng bảng giá, combo và chính sách ship theo khu vực',
                    'Theo dõi tồn nguyên liệu, hạn sử dụng và lô đóng gói thủ công',
                    'Chăm sóc khách hàng sau bán (hướng dẫn pha, gợi ý mua lại)',
                    'Đối soát doanh thu – chi phí ship – tồn cuối tuần',
                ],
                'en' => [
                    'Set pricing, bundles, and shipping rules by area',
                    'Tracked ingredients, shelf life, and manual batch packing',
                    'Post-sale support (brewing tips, reorder suggestions)',
                    'Weekly reconciliation of revenue, shipping cost, and stock',
                ],
            ],
            'tech_stack' => ['Bán hàng online', 'Đóng gói', 'Excel'],
        ],
        [
            'title' => [
                'vi' => 'Tiếp xúc vận hành kho & logistics',
                'en' => 'Warehouse & logistics exposure',
            ],
            'subtitle' => [
                'vi' => 'Kinh nghiệm thực tế còn ngắn — đang học nghiệp vụ',
                'en' => 'Limited floor time—building operations skills',
            ],
            'period' => '2023 – 2024',
            'solution' => [
                'vi' => 'Tham gia <strong>hỗ trợ</strong> công việc kho: nhận hàng, soạn – đóng gói, cập nhật tồn theo chỉ dẫn; chưa đảm nhiệm vai trò quản lý hay giám sát đội.',
                'en' => '<strong>Assisted</strong> warehouse tasks: receiving, pick-pack, stock updates with guidance—not a supervisor or team lead role.',
            ],
            'highlights' => [
                'vi' => [
                    'Làm quen quy trình nhập – xuất – lưu hàng và bàn giao cho vận chuyển',
                    'Tham gia kiểm kê đơn giản, báo chênh lệch cho người phụ trách',
                    'Học cách đọc mã hàng, vị trí và cập nhật trên hệ thống cơ bản',
                ],
                'en' => [
                    'Learned inbound/outbound/storage and handoff to carriers',
                    'Joined simple cycle counts and reported variances to supervisors',
                    'Practiced item codes, locations, and basic system updates',
                ],
            ],
            'tech_stack' => ['Nhập – xuất – tồn', 'Hỗ trợ vận hành'],
        ],
        [
            'title' => [
                'vi' => 'Triển khai hệ thống quản lý kho – TOLINEN (Nhật Bản)',
                'en' => 'Warehouse system rollout – TOLINEN (Japan)',
            ],
            'subtitle' => [
                'vi' => 'Lập trình nâng cấp WMS | Chuyển đổi số kho | Khách hàng Nhật',
                'en' => 'WMS development & upgrade | Japanese client',
            ],
            'period' => '10/2025 – 04/2026',
            'solution' => [
                'vi' => 'Hỗ trợ <strong>triển khai và nâng cấp hệ thống quản lý kho (WMS)</strong> cho khách Nhật: ghi nhận yêu cầu nghiệp vụ, kiểm thử module quản lý nhập – xuất – tồn, hướng tới <strong>số hóa</strong> vận hành kho trên hệ thống.',
                'en' => 'Supported <strong>WMS rollout and upgrades</strong> for a Japanese client—business requirements, testing inventory <strong>management</strong> modules, advancing warehouse <strong>digitization</strong>.',
            ],
            'highlights' => [
                'vi' => [
                    'Học quy trình kho thực tế qua mô tả nghiệp vụ — chuẩn bị triển khai hệ thống quản lý',
                    'Kiểm thử màn hình tra cứu tồn, điều chuyển và kiểm kê trước bàn giao hệ thống',
                    'Hỗ trợ chỉnh sửa nhỏ module nhập/xuất dưới hướng dẫn senior',
                    'Phối hợp trao đổi với khách hàng bằng ngôn ngữ nghiệp vụ, không chỉ thuật ngữ IT',
                ],
                'en' => [
                    'Learned floor flows from PM and user descriptions',
                    'Tested stock lookup, transfers, and count scenarios before release',
                    'Assisted with small inbound/outbound fixes under senior guidance',
                    'Communicated with the client in operational terms',
                ],
            ],
            'tech_stack' => ['WMS', 'Nâng cấp hệ thống', 'Nhập – xuất – tồn'],
        ],
        [
            'title' => [
                'vi' => 'Cải tiến quy trình quản lý đơn & phân quyền bộ phận – Công ty Quảng cáo 3M',
                'en' => 'Order workflow & department access – 3M Advertising',
            ],
            'subtitle' => [
                'vi' => 'Lập trình nâng cấp hệ thống quản lý đơn | Phân quyền bộ phận',
                'en' => 'Order system upgrade development | Department access',
            ],
            'period' => '09/2025 – 11/2025',
            'solution' => [
                'vi' => 'Tham gia <strong>nâng cấp hệ thống quản lý</strong> đơn/hóa đơn công ty – chi nhánh: <strong>số hóa</strong> luồng <strong>kinh doanh</strong>, phân quyền bộ phận và kiểm thử đối chiếu dữ liệu.',
                'en' => 'Helped <strong>upgrade the order/invoice management system</strong> for HQ and branches—<strong>digitizing</strong> <strong>business</strong> workflows, permissions, and reconciliation testing.',
            ],
            'highlights' => [
                'vi' => [
                    'Làm rõ luồng xử lý đơn/hóa đơn với người dùng nghiệp vụ trước khi chỉnh hệ thống',
                    'Lập trình chức năng quản lý đơn và phân quyền theo phòng ban/chi nhánh',
                    'Hỗ trợ đối chiếu số liệu, xử lý phát sinh khi triển khai nâng cấp',
                ],
                'en' => [
                    'Clarified order/invoice flows with business users before system changes',
                    'Developed order features and department/branch access rules',
                    'Supported reconciliation and rollout exceptions',
                ],
            ],
            'tech_stack' => ['Nâng cấp hệ thống', 'Quy trình đơn', 'Phân quyền'],
        ],
    ],
    'extra_principles' => [],
    'cv_list_items' => [
        'certification' => [
            ['title' => ['vi' => 'Giấy phép lái xe ô tô B2', 'en' => 'Category B2 driving license']],
            ['title' => ['vi' => 'Chứng chỉ/Giấy phép vận hành xe nâng', 'en' => 'Forklift operation license/certificate']],
            ['title' => ['vi' => 'Đang học và hỗ trợ vận hành WMS (key user cơ bản)', 'en' => 'Learning and supporting WMS as a junior key user']],
            ['title' => ['vi' => 'Kinh nghiệm sử dụng MYBS, Microsoft Dynamics AX và Microsoft Dynamics 365', 'en' => 'Experience with MYBS, Microsoft Dynamics AX, and Dynamics 365']],
        ],
        'career_target' => [
            ['title' => ['vi' => 'Nhân viên kinh doanh / Chăm sóc khách hàng (online)', 'en' => 'Sales / Online customer care']],
            ['title' => ['vi' => 'Nhân viên vận hành kho / Logistics (junior)', 'en' => 'Junior warehouse / logistics staff']],
            ['title' => ['vi' => 'WMS Key User / IT nghiệp vụ', 'en' => 'WMS key user / business IT support']],
            ['title' => ['vi' => 'Thực tập / Fresher lập trình PHP – Laravel', 'en' => 'PHP–Laravel intern / junior developer']],
            ['title' => ['vi' => 'Nhân viên phát triển kinh doanh online', 'en' => 'Online business development staff']],
        ],
        'strength' => [
            ['title' => ['vi' => 'Triển khai hệ thống TMĐT & Affiliate (Tập đoàn Liên Hoa) và kinh doanh online — nắm luồng đơn hàng và quản lý kinh doanh thực tế', 'en' => 'Lien Hoa Group system rollout and online sales—real order flows and business management']],
            ['title' => ['vi' => 'QTKD – tin học; chuyển nhanh quy trình kinh doanh/vận hành thành yêu cầu triển khai hệ thống', 'en' => 'Business/IT background; maps ops flows to system rollout requirements']],
            ['title' => ['vi' => 'Hỗ trợ nâng cấp phần mềm quản lý: kiểm thử, sửa lỗi, ghi nhận yêu cầu — hướng số hóa quy trình', 'en' => 'Supports Laravel management upgrades—testing, fixes, requirements toward process digitization']],
            ['title' => ['vi' => 'Theo dõi đơn, tồn, đối soát (Excel); sẵn sàng làm việc trên hệ thống WMS/ERP quản lý', 'en' => 'Order, stock, reconciliation (Excel); ready for WMS/ERP management systems']],
            ['title' => ['vi' => 'Đang bổ sung nghiệp vụ kho; trung thực về kinh nghiệm, chủ động học quy trình trên hệ thống', 'en' => 'Building warehouse skills; honest experience level, proactive learning on systems']],
            ['title' => ['vi' => 'Phối hợp đội vận hành và IT khi triển khai, nâng cấp hệ thống phần mềm quản lý', 'en' => 'Works with ops and IT on management system rollout and upgrades']],
        ],
    ],
];
