<?php

/**
 * CV overlay: nghiệp vụ kho vận là trọng tâm; lập trình như năng lực để chuyển đổi / nâng cấp hệ thống kho.
 */
return [
    'skip_legacy_skill_categories' => true,
    'replace_legacy_projects' => true,
    'skip_legacy_principles' => true,

    'profile' => [
        'headline' => [
            'vi' => 'Kho vận & Logistics | Nghiệp vụ kho + Lập trình nâng cấp hệ thống quản lý kho',
            'en' => 'Warehouse & Logistics | Operations plus software for warehouse system upgrades',
        ],
        'tagline' => [
            'vi' => 'Vừa am hiểu vận hành kho (nhập – xuất – tồn, WMS), vừa có kinh nghiệm lập trình để triển khai, nâng cấp phần mềm kho — giúp chuyển đổi số bám sát thực tế hiện trường.',
            'en' => 'Combines warehouse operations (inbound/outbound/inventory, WMS) with hands-on development to roll out and upgrade warehouse software aligned with the floor.',
        ],
        'about' => [
            'vi' => '<p>Có kinh nghiệm <strong>vận hành và quản lý kho bãi, logistics</strong>: kiểm soát nhập hàng, xuất hàng, lưu trữ, soạn hàng, đóng gói và giao hàng; theo dõi tồn kho, vị trí hàng và đối chiếu với số liệu trên hệ thống.</p>'
                .'<p>Tham gia <strong>kiểm kê, xử lý thiếu/thừa hàng</strong>, phân tích nguyên nhân sai lệch và lập báo cáo vận hành, tồn kho theo yêu cầu. Phối hợp chặt giữa kho – vận tải – kế hoạch – khách hàng và các bộ phận liên quan.</p>'
                .'<p>Bên cạnh nghiệp vụ tại kho, có kinh nghiệm <strong>triển khai và cải tiến hệ thống quản lý kho (WMS)</strong> cho doanh nghiệp (gồm dự án khách hàng Nhật Bản): chuẩn hóa thao tác trên hệ thống, hỗ trợ người dùng, đối chiếu dữ liệu thực tế với phần mềm và đề xuất cải tiến quy trình (SOP).</p>'
                .'<p><strong>Điểm khác biệt:</strong> có nền tảng <strong>lập trình phần mềm</strong> (PHP, Laravel và hệ thống web quản lý) để tham gia <strong>chuyển đổi số / nâng cấp phần mềm kho</strong>: chuyển yêu cầu nghiệp vụ nhập – xuất – tồn thành chức năng trên hệ thống, bảo trì và mở rộng module WMS, xử lý sai lệch dữ liệu tồn, hỗ trợ báo cáo vận hành — không chỉ dừng ở thao tác key user.</p>'
                .'<p>Mục tiêu: tối ưu quy trình kho vận, nâng độ chính xác tồn kho, năng suất và chất lượng giao nhận; làm cầu nối giữa <strong>đội vận hành – quy trình – đội phát triển hệ thống</strong>.</p>',
            'en' => '<p>Experienced in <strong>warehouse and logistics operations</strong>: inbound and outbound control, storage, picking, packing, and dispatch; stock and location tracking with reconciliation against system records.</p>'
                .'<p>Conducts <strong>cycle counts</strong>, investigates shortages and overages, analyzes root causes, and prepares operational and inventory reports. Coordinates warehouse, transport, planning, customers, and internal teams.</p>'
                .'<p>Alongside floor work, contributed to <strong>WMS rollout and improvement</strong> (including a Japan-based client): standardizing system steps, user support, stock reconciliation, and SOP updates.</p>'
                .'<p><strong>Differentiator:</strong> <strong>software development</strong> background (PHP, Laravel, management web apps) to support <strong>warehouse digital transformation and system upgrades</strong>—turning inbound/outbound/inventory needs into system features, maintaining and extending WMS modules, fixing stock data issues, and supporting operational reporting—not only key-user operation.</p>'
                .'<p>Goal: better warehouse performance and inventory accuracy; bridge <strong>operations, process, and the team that builds the system</strong>.</p>',
        ],
        'philosophy_quote' => [
            'vi' => 'Hiểu kho thì mới nâng cấp phần mềm kho đúng; hiểu lập trình thì mới chuyển đổi số quy trình một cách bền vững.',
            'en' => 'Warehouse knowledge shapes the right system upgrades; development skills make digital change sustainable.',
        ],
    ],
    'cv_settings' => [
        'show_principles' => false,
        'show_certifications' => true,
        'show_career_targets' => true,
        'show_strengths' => true,
    ],
    'skill_categories' => [
        [
            'name' => ['vi' => 'Nghiệp vụ kho & tồn kho', 'en' => 'Warehouse & inventory operations'],
            'sort_order' => 0,
            'skills' => [
                [
                    'name' => 'Nhập – xuất – tồn',
                    'description' => [
                        'vi' => 'Nhận hàng, cất hàng, soạn – đóng gói, xuất kho, theo dõi biến động tồn',
                        'en' => 'Receiving, put-away, pick-pack, shipping, and stock movement control',
                    ],
                    'level' => 78,
                ],
                [
                    'name' => 'Kiểm kê & đối chiếu',
                    'description' => [
                        'vi' => 'Kiểm kê định kỳ/đột xuất, xử lý chênh lệch, báo cáo tồn',
                        'en' => 'Scheduled and ad-hoc counts, variance handling, stock reporting',
                    ],
                    'level' => 75,
                ],
                [
                    'name' => 'Quản lý vị trí & hàng hóa',
                    'description' => [
                        'vi' => 'Theo dõi mã hàng, vị trí lưu trữ, tình trạng hàng, hạn sử dụng (nếu có)',
                        'en' => 'Item codes, storage locations, stock condition, expiry where applicable',
                    ],
                    'level' => 72,
                ],
            ],
        ],
        [
            'name' => ['vi' => 'Vận tải & điều phối', 'en' => 'Transport & coordination'],
            'sort_order' => 1,
            'skills' => [
                [
                    'name' => 'Kế hoạch giao nhận',
                    'description' => [
                        'vi' => 'Sắp lịch xe, theo dõi tiến độ giao – nhận, xử lý trễ/sai thông tin',
                        'en' => 'Vehicle scheduling, delivery progress, delays and data corrections',
                    ],
                    'level' => 70,
                ],
                [
                    'name' => 'Phối hợp liên bộ phận',
                    'description' => [
                        'vi' => 'Làm việc với kế hoạch, kinh doanh, kho khác và đơn vị vận chuyển',
                        'en' => 'Planning, sales, other sites, and carriers',
                    ],
                    'level' => 74,
                ],
            ],
        ],
        [
            'name' => ['vi' => 'WMS & hệ thống doanh nghiệp', 'en' => 'WMS & enterprise systems'],
            'sort_order' => 2,
            'skills' => [
                [
                    'name' => 'Vận hành WMS',
                    'description' => [
                        'vi' => 'Thao tác nhập – xuất – tồn trên hệ thống, điều chuyển, xử lý lỗi phát sinh',
                        'en' => 'System-side inbound/outbound/inventory, transfers, exception handling',
                    ],
                    'level' => 72,
                ],
                [
                    'name' => 'MYBS / Dynamics AX / D365',
                    'description' => [
                        'vi' => 'Tra cứu, đối chiếu dữ liệu nghiệp vụ và báo cáo theo phân quyền',
                        'en' => 'Lookup, reconciliation, and reporting within role-based access',
                    ],
                    'level' => 60,
                ],
                [
                    'name' => 'Key user & đào tạo',
                    'description' => [
                        'vi' => 'Hướng dẫn thao tác, hỗ trợ đồng nghiệp, phối hợp IT khi cần cải tiến',
                        'en' => 'User guidance, peer training, IT coordination for improvements',
                    ],
                    'level' => 68,
                ],
            ],
        ],
        [
            'name' => ['vi' => 'Lập trình phục vụ kho vận & chuyển đổi số', 'en' => 'Development for warehouse digitalization'],
            'sort_order' => 3,
            'skills' => [
                [
                    'name' => 'Nghiệp vụ → chức năng hệ thống',
                    'description' => [
                        'vi' => 'Làm rõ quy trình kho với đội vận hành, mô tả yêu cầu để triển khai trên WMS/phần mềm quản lý',
                        'en' => 'Clarify warehouse flows with operators and specify requirements for WMS/management software',
                    ],
                    'level' => 74,
                ],
                [
                    'name' => 'Nâng cấp & bảo trì phần mềm kho',
                    'description' => [
                        'vi' => 'Phát triển, chỉnh sửa module nhập – xuất – tồn; sửa lỗi vận hành; cải thiện tra cứu tồn và lịch sử giao dịch',
                        'en' => 'Build and maintain inbound/outbound/inventory modules; fix production issues; improve stock and history lookup',
                    ],
                    'level' => 72,
                ],
                [
                    'name' => 'PHP / Laravel (web quản lý)',
                    'description' => [
                        'vi' => 'Xây dựng và mở rộng ứng dụng web quản lý, đơn hàng, phân quyền bộ phận — áp dụng khi doanh nghiệp cần số hóa',
                        'en' => 'Management web apps, orders, and department access—when the business needs digitization',
                    ],
                    'level' => 70,
                ],
                [
                    'name' => 'Dữ liệu tồn kho & báo cáo',
                    'description' => [
                        'vi' => 'Thiết kế lưu trữ dữ liệu kho, đối chiếu số liệu, hỗ trợ báo cáo phục vụ giám sát vận hành',
                        'en' => 'Warehouse data structure, reconciliation, and reporting for operational oversight',
                    ],
                    'level' => 68,
                ],
            ],
        ],
        [
            'name' => ['vi' => 'Quản lý vận hành & cải tiến', 'en' => 'Operations management & improvement'],
            'sort_order' => 4,
            'skills' => [
                [
                    'name' => 'SOP & chuẩn hóa quy trình',
                    'description' => [
                        'vi' => 'Xây dựng, cập nhật quy trình thao tác chuẩn trong kho vận',
                        'en' => 'Developing and updating standard warehouse procedures',
                    ],
                    'level' => 70,
                ],
                [
                    'name' => 'Báo cáo & ra quyết định',
                    'description' => [
                        'vi' => 'Báo cáo vận hành, tồn kho; đề xuất giải pháp khi có sự cố',
                        'en' => 'Operational and inventory reports; practical issue resolution',
                    ],
                    'level' => 72,
                ],
            ],
        ],
    ],
    'projects' => [
        [
            'title' => [
                'vi' => 'Vận hành kho bãi & Logistics',
                'en' => 'Warehouse & logistics operations',
            ],
            'subtitle' => [
                'vi' => 'Kinh nghiệm nghiệp vụ tại kho – vận tải',
                'en' => 'Floor and coordination experience',
            ],
            'period' => '2018 – 2023',
            'solution' => [
                'vi' => 'Trực tiếp tham gia điều hành hoạt động kho hàng ngày: kiểm soát nhập – xuất – lưu trữ, soạn hàng, giao nhận và theo dõi tồn kho theo thực tế và trên hệ thống.',
                'en' => 'Daily warehouse execution: inbound/outbound/storage, picking, dispatch, and stock control on the floor and in the system.',
            ],
            'highlights' => [
                'vi' => [
                    'Quản lý và giám sát hoạt động kho; đảm bảo tiến độ xử lý đơn và xuất hàng',
                    'Kiểm soát quy trình nhận hàng, cất hàng, soạn – đóng gói và bàn giao vận chuyển',
                    'Theo dõi vị trí hàng, tình trạng tồn; đối chiếu số liệu thực tế với hệ thống',
                    'Phối hợp điều phối xe, kế hoạch giao nhận; xử lý sự cố trễ hàng, sai thông tin',
                    'Tổ chức kiểm kê, phân tích thiếu/thừa và lập báo cáo vận hành, tồn kho',
                    'Tham gia xây dựng và cải tiến SOP thao tác chuẩn',
                ],
                'en' => [
                    'Supervised daily warehouse activity and order/shipping timelines',
                    'Controlled receiving, put-away, pick-pack, and handover to transport',
                    'Tracked locations and stock; reconciled physical counts with system data',
                    'Coordinated vehicles and delivery plans; handled delays and data errors',
                    'Organized cycle counts, analyzed variances, and reported operations and stock',
                    'Contributed to warehouse SOP development and updates',
                ],
            ],
            'tech_stack' => ['Nhập – xuất – tồn', 'Kiểm kê', 'SOP'],
        ],
        [
            'title' => [
                'vi' => 'Quản trị & vận hành WMS (Key user)',
                'en' => 'WMS administration (key user)',
            ],
            'subtitle' => [
                'vi' => 'Hệ thống quản lý kho doanh nghiệp',
                'en' => 'Enterprise warehouse management system',
            ],
            'period' => '2020 – Hiện tại',
            'solution' => [
                'vi' => 'Đảm nhiệm vai trò key user WMS: quản lý thông tin hàng hóa và vị trí lưu trữ, vận hành nghiệp vụ nhập – xuất – tồn, điều chuyển hàng và kiểm soát dữ liệu tồn trên hệ thống.',
                'en' => 'WMS key user: item and location master, inbound/outbound/inventory transactions, transfers, and system stock control.',
            ],
            'highlights' => [
                'vi' => [
                    'Theo dõi nhập kho, cất hàng, soạn hàng, đóng gói và xuất kho trên WMS',
                    'Quản lý điều chuyển giữa vị trí/kho; kiểm kê và đối chiếu tồn',
                    'Kiểm tra chênh lệch giữa hàng thực tế và dữ liệu hệ thống',
                    'Hỗ trợ, hướng dẫn và đào tạo thao tác WMS cho đội vận hành',
                    'Phối hợp IT/nhà cung cấp khi phát sinh lỗi hoặc yêu cầu cải tiến quy trình',
                ],
                'en' => [
                    'Managed inbound, put-away, picking, packing, and outbound in WMS',
                    'Handled inter-location transfers; counts and stock reconciliation',
                    'Verified variances between floor stock and system records',
                    'Supported and trained operators on WMS procedures',
                    'Worked with IT/vendors on defects and process improvement requests',
                ],
            ],
            'tech_stack' => ['WMS', 'Tồn kho', 'Điều chuyển'],
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
                'vi' => 'Tham gia <strong>bảo trì và nâng cấp phần mềm quản lý kho</strong> cho doanh nghiệp Nhật Bản, kết hợp hiểu biết nghiệp vụ nhập – xuất – tồn với triển khai chức năng trên hệ thống: lịch sử giao dịch kho, kiểm soát sai lệch tồn và quy trình vận hành thực tế.',
                'en' => 'Maintained and <strong>upgraded warehouse management software</strong> for a Japanese company—pairing inbound/outbound/inventory know-how with system delivery: transaction history, stock variance control, and real floor practice.',
            ],
            'highlights' => [
                'vi' => [
                    'Phân tích nghiệp vụ kho với PM/khách hàng; chuyển yêu cầu vận hành thành chỉnh sửa chức năng trên WMS',
                    'Lập trình bổ sung/cải tiến module nhập kho, xuất kho, tồn kho và tra cứu lịch sử xuất nhập',
                    'Xử lý lỗi phát sinh khi vận hành; tối ưu thao tác tra cứu tồn giúp đội kho làm việc nhanh hơn',
                    'Kiểm thử kịch bản kiểm kê, điều chuyển hàng trước bàn giao — đảm bảo phần mềm khớp quy trình kho',
                    'Phối hợp khách hàng Nhật: giải thích thao tác hệ thống bằng ngôn ngữ nghiệp vụ kho, không chỉ thuật ngữ kỹ thuật',
                ],
                'en' => [
                    'Analyzed warehouse requirements with PM/client; turned operational needs into WMS changes',
                    'Developed and improved inbound, outbound, inventory, and transaction-history features',
                    'Fixed production issues; streamlined stock lookup for faster floor work',
                    'Tested cycle-count and transfer scenarios before release—software matched warehouse steps',
                    'Worked with Japanese stakeholders using warehouse language, not only technical jargon',
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
                'vi' => 'Tham gia <strong>nâng cấp phần mềm quản lý hóa đơn/đơn hàng</strong> theo mô hình công ty – chi nhánh: số hóa luồng xử lý, phân quyền theo bộ phận và hỗ trợ đối chiếu dữ liệu — giảm nhầm lẫn trong vận hành hàng ngày (liên quan xử lý đơn, kho và giao nhận).',
                'en' => 'Helped <strong>upgrade order/invoice management software</strong> for company and branches: digitized workflows, department permissions, and reconciliation—fewer day-to-day mix-ups tied to orders, warehouse, and dispatch.',
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
            ['title' => ['vi' => 'Kinh nghiệm thực tế vận hành và quản trị hệ thống WMS', 'en' => 'Hands-on WMS operations and administration']],
            ['title' => ['vi' => 'Kinh nghiệm sử dụng MYBS, Microsoft Dynamics AX và Microsoft Dynamics 365', 'en' => 'Experience with MYBS, Microsoft Dynamics AX, and Dynamics 365']],
        ],
        'career_target' => [
            ['title' => ['vi' => 'Warehouse Supervisor / Warehouse Manager', 'en' => 'Warehouse Supervisor / Warehouse Manager']],
            ['title' => ['vi' => 'Logistics Supervisor / Logistics Operation Supervisor', 'en' => 'Logistics Supervisor / Logistics Operation Supervisor']],
            ['title' => ['vi' => 'Transport Supervisor', 'en' => 'Transport Supervisor']],
            ['title' => ['vi' => 'WMS Key User / WMS Supervisor', 'en' => 'WMS Key User / WMS Supervisor']],
            ['title' => ['vi' => 'Logistics Operation Manager / Operation Supervisor', 'en' => 'Logistics Operation Manager / Operation Supervisor']],
        ],
        'strength' => [
            ['title' => ['vi' => 'Nền tảng Quản trị kinh doanh – Tin học – Logistics; làm cầu nối giữa sàn kho và đội IT/phát triển', 'en' => 'Business, IT, and logistics background—bridges the warehouse floor and development/IT']],
            ['title' => ['vi' => 'Phân tích nghiệp vụ nhập–xuất–tồn, chuyển thành yêu cầu chức năng trên WMS và phần mềm quản lý', 'en' => 'Turns inbound/outbound/inventory flows into WMS and management software requirements']],
            ['title' => ['vi' => 'Phát triển & bảo trì ứng dụng web (PHP, Laravel): module kho, đơn hàng, tra cứu tồn và báo cáo vận hành', 'en' => 'Builds and maintains web apps (PHP, Laravel): warehouse modules, orders, stock lookup, and ops reporting']],
            ['title' => ['vi' => 'Key user WMS/ERP (MYBS, Dynamics AX/365): hướng dẫn thao tác, xử lý lỗi vận hành, đối chiếu dữ liệu tồn', 'en' => 'WMS/ERP key user (MYBS, Dynamics AX/365): training, production issue handling, stock data reconciliation']],
            ['title' => ['vi' => 'Tư duy số hóa quy trình: cải tiến SOP, giảm thao tác thủ công, khai thác dữ liệu hệ thống', 'en' => 'Digital process mindset: SOP improvements, less manual work, better use of system data']],
            ['title' => ['vi' => 'Chủ động phát hiện sai lệch; phối hợp kho – kế hoạch – IT khi triển khai và nâng cấp hệ thống', 'en' => 'Proactive on variances; coordinates warehouse, planning, and IT during rollout and upgrades']],
        ],
    ],
];
