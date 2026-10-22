<?php

/**
 * CV overlay: warehouse / logistics domain + digital transformation (WMS, backend).
 * Inspired by logistics CV structure; tailored for Nguyễn Đức Độ demo portfolio.
 */
return [
    'profile' => [
        'headline' => [
            'vi' => 'Lập trình viên Backend | Chuyển đổi số Kho vận & WMS',
            'en' => 'Backend Developer | Warehouse & Logistics Digital Transformation',
        ],
        'tagline' => [
            'vi' => 'Kết hợp hiểu biết nghiệp vụ kho – vận tải với phát triển hệ thống (Laravel, API, WMS) để số hóa quy trình nhập – xuất – tồn.',
            'en' => 'Bridging warehouse & logistics operations with Laravel, APIs, and WMS delivery to digitize inbound, outbound, and inventory flows.',
        ],
        'about' => [
            'vi' => '<p>Lập trình viên Backend (PHP, Laravel) với định hướng <strong>chuyển đổi số trong kho vận và logistics</strong>. Có kinh nghiệm nghiệp vụ vận hành kho: kiểm soát nhập – xuất – tồn, picking/packing, kiểm kê, đối chiếu dữ liệu thực tế với hệ thống, phối hợp kho – vận tải – kế hoạch.</p>'
                .'<p>Đã triển khai và bảo trì <strong>hệ thống quản lý kho (WMS)</strong> cho khách hàng Nhật Bản, mapping quy trình Inbound/Outbound/Inventory lên phần mềm, tối ưu truy vấn và logic nghiệp vụ. Quen làm việc với REST/GraphQL API, MySQL, Redis; ưu tiên độ chính xác dữ liệu tồn kho và khả năng mở rộng khi quy trình thay đổi.</p>'
                .'<p>Mục tiêu: đóng vai trò cầu nối giữa <strong>con người – quy trình – hệ thống</strong>, hỗ trợ doanh nghiệp nâng cao năng suất kho vận thông qua phần mềm và cải tiến SOP.</p>',
            'en' => '<p>Backend developer (PHP, Laravel) focused on <strong>digital transformation in warehouse and logistics</strong>. Hands-on with warehouse operations: inbound/outbound/inventory control, picking and packing, cycle counts, reconciling physical stock with system data, and coordinating warehouse, transport, and planning teams.</p>'
                .'<p>Built and maintained a <strong>WMS</strong> for a Japanese client, mapping Inbound/Outbound/Inventory flows into software, optimizing queries and business logic. Comfortable with REST/GraphQL APIs, MySQL, and Redis; prioritizes inventory accuracy and systems that evolve with operational change.</p>'
                .'<p>Goal: connect <strong>people, process, and systems</strong> to improve logistics productivity through software and standardized operating procedures.</p>',
        ],
    ],
    'cv_settings' => [
        'show_principles' => true,
    ],
    'skill_categories' => [
        [
            'name' => ['vi' => 'Kho vận, WMS & Chuyển đổi số', 'en' => 'Warehouse, WMS & Digital Transformation'],
            'sort_order' => 0,
            'skills' => [
                [
                    'name' => 'Nghiệp vụ nhập – xuất – tồn',
                    'description' => [
                        'vi' => 'Put-away, picking, packing, điều chuyển, kiểm kê, đối chiếu sai lệch',
                        'en' => 'Put-away, picking, packing, transfers, cycle counts, variance reconciliation',
                    ],
                    'level' => 72,
                ],
                [
                    'name' => 'WMS & vị trí lưu trữ',
                    'description' => [
                        'vi' => 'Quản lý SKU, bin/location, lịch sử giao dịch kho, xử lý lỗi thao tác',
                        'en' => 'SKU and location management, stock transaction history, operational exception handling',
                    ],
                    'level' => 70,
                ],
                [
                    'name' => 'ERP / hệ thống doanh nghiệp',
                    'description' => [
                        'vi' => 'Hiểu luồng dữ liệu WMS–ERP (Dynamics, MYBS…); báo cáo vận hành & tồn kho',
                        'en' => 'WMS–ERP data flows (Dynamics, MYBS, etc.); operational and inventory reporting',
                    ],
                    'level' => 58,
                ],
                [
                    'name' => 'Số hóa quy trình & SOP',
                    'description' => [
                        'vi' => 'Phân tích nghiệp vụ, đề xuất cải tiến, chuẩn hóa thao tác trên hệ thống',
                        'en' => 'Process analysis, improvement proposals, standardizing workflows in software',
                    ],
                    'level' => 68,
                ],
            ],
        ],
    ],
    'prepend_projects' => [
        [
            'title' => [
                'vi' => 'Vận hành kho bãi & Logistics (nghiệp vụ)',
                'en' => 'Warehouse & Logistics Operations (domain experience)',
            ],
            'subtitle' => [
                'vi' => 'Kinh nghiệm thực tế tại kho – vận tải',
                'en' => 'Hands-on warehouse and transport operations',
            ],
            'period' => '2018 – 2023',
            'solution' => [
                'vi' => 'Tham gia trực tiếp vận hành và điều phối hoạt động kho vận: kiểm soát tiến độ giao nhận, theo dõi tồn kho và xử lý sự cố phát sinh. Kinh nghiệm này là nền tảng khi thiết kế và triển khai phần mềm WMS/ERP.',
                'en' => 'Direct involvement in warehouse and logistics execution: delivery scheduling, stock monitoring, and exception handling. This operational background informs WMS/ERP design and implementation.',
            ],
            'highlights' => [
                'vi' => [
                    'Quản lý và theo dõi hoạt động kho hàng ngày; kiểm soát nhập – xuất – lưu trữ – giao hàng',
                    'Theo dõi vị trí hàng, tình trạng tồn kho và đối chiếu với dữ liệu hệ thống',
                    'Phối hợp điều phối phương tiện, kế hoạch giao nhận và các bộ phận liên quan',
                    'Kiểm kê, phân tích nguyên nhân thiếu/thừa hàng và lập báo cáo vận hành',
                    'Hỗ trợ xây dựng và cải tiến SOP thao tác chuẩn trong kho vận',
                ],
                'en' => [
                    'Managed day-to-day warehouse activity; controlled inbound, outbound, storage, and dispatch',
                    'Tracked locations, stock condition, and reconciled with system records',
                    'Coordinated vehicles, delivery plans, and cross-functional teams',
                    'Performed cycle counts, analyzed shrinkage/overages, and produced operational reports',
                    'Contributed to warehouse SOP design and continuous improvement',
                ],
            ],
            'tech_stack' => ['WMS', 'Inventory', 'SOP', 'Logistics'],
        ],
    ],
    'project_patches' => [
        'TOLINEN' => [
            'subtitle' => [
                'vi' => 'Chuyển đổi số quản lý kho | Khách hàng Nhật Bản',
                'en' => 'Warehouse digitalization | Japan-based client',
            ],
            'solution' => [
                'vi' => 'Phát triển và bảo trì WMS theo nghiệp vụ Inbound – Outbound – Inventory: quản lý hàng hóa, lịch sử xuất nhập, tồn kho và kiểm soát sai lệch giữa thực tế và hệ thống.',
                'en' => 'Developed and maintained a WMS covering Inbound, Outbound, and Inventory: product master, stock movements, on-hand balances, and variance control between floor and system.',
            ],
            'highlights' => [
                'vi' => [
                    'Mapping quy trình nhập kho, put-away, picking, packing và xuất kho lên module hệ thống',
                    'Cải tiến truy vấn và logic nghiệp vụ; giảm thời gian tra cứu tồn và lịch sử giao dịch',
                    'Xử lý yêu cầu thay đổi quy trình từ khách hàng; đảm bảo dữ liệu nhất quán khi vận hành',
                    'Phối hợp PM/QA để kiểm thử các kịch bản kiểm kê và điều chuyển hàng',
                ],
                'en' => [
                    'Mapped inbound, put-away, picking, packing, and outbound flows into system modules',
                    'Improved queries and business logic; faster stock and transaction lookups',
                    'Implemented process changes from the client while keeping data consistent in production',
                    'Worked with PM/QA on cycle-count and stock-transfer test scenarios',
                ],
            ],
            'tech_stack' => ['CodeIgniter', 'MySQL', 'JavaScript', 'WMS'],
        ],
    ],
    'extra_principles' => [
        [
            'title' => ['vi' => 'Con người – Quy trình – Hệ thống', 'en' => 'People, process, and systems'],
            'description' => [
                'vi' => 'Phần mềm kho vận chỉ hiệu quả khi bám sát thao tác thực tế và được người vận hành tin dùng.',
                'en' => 'Warehouse software works when it mirrors real operations and earns trust from floor teams.',
            ],
        ],
        [
            'title' => ['vi' => 'Độ chính xác tồn kho', 'en' => 'Inventory accuracy'],
            'description' => [
                'vi' => 'Ưu tiên kiểm soát sai lệch, audit trail và báo cáo rõ ràng cho quyết định vận hành.',
                'en' => 'Focus on variance control, audit trails, and clear reporting for operational decisions.',
            ],
        ],
        [
            'title' => ['vi' => 'Cải tiến liên tục', 'en' => 'Continuous improvement'],
            'description' => [
                'vi' => 'Tư duy phân tích nguyên nhân và đề xuất giải pháp — trên kho cũng như trong code.',
                'en' => 'Root-cause thinking and practical fixes — on the warehouse floor and in code.',
            ],
        ],
    ],
];
