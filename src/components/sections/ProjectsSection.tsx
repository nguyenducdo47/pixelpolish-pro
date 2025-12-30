import { motion } from "framer-motion";
import { Layers, Shield, ShoppingCart } from "lucide-react";

const ProjectsSection = () => {
  const projects = [
    {
      icon: Layers,
      title: "Hệ thống Quản lý Đơn hàng Kho In",
      subtitle: "Printing Warehouse Order Management System",
      complexity: "Complex • Team Project",
      problem: "Nhà in cần quản lý luồng đơn hàng phức tạp từ tiếp nhận, sản xuất đến giao hàng với nhiều trạng thái và người phụ trách khác nhau.",
      solution: "Xây dựng hệ thống theo dõi vòng đời đơn hàng với state machine, phân quyền theo bộ phận, và dashboard thống kê real-time.",
      techStack: ["Laravel", "MySQL", "Redis", "Blade", "TailwindCSS"],
      responsibilities: [
        "Thiết kế database schema cho order lifecycle",
        "Xây dựng API quản lý trạng thái đơn hàng",
        "Tối ưu query với Redis caching",
      ],
      learned: "Hiểu sâu về state management, database transaction và cách thiết kế hệ thống có thể scale.",
    },
    {
      icon: Shield,
      title: "Hệ thống Quản lý Trường học",
      subtitle: "School Management System",
      complexity: "Medium • Team Project",
      problem: "Trường học cần hệ thống quản lý học sinh, giáo viên, điểm số với nhiều vai trò người dùng khác nhau.",
      solution: "Phát triển hệ thống RBAC với 3 role chính (Admin, Teacher, Student), tối ưu CRUD operations và validation chặt chẽ.",
      techStack: ["Laravel", "MySQL", "Blade", "Bootstrap"],
      responsibilities: [
        "Thiết kế hệ thống phân quyền RBAC",
        "Xây dựng module quản lý điểm và attendance",
        "Implement form validation và security",
      ],
      learned: "Kinh nghiệm về authorization, security best practices và cách tổ chức code sạch.",
    },
    {
      icon: ShoppingCart,
      title: "Website Thương mại Điện tử",
      subtitle: "E-commerce Website",
      complexity: "Learning • Personal Project",
      problem: "Xây dựng nền tảng bán hàng online với đầy đủ chức năng từ hiển thị sản phẩm đến thanh toán.",
      solution: "Áp dụng Laravel MVC pattern, authentication system, shopping cart và tích hợp payment gateway.",
      techStack: ["Laravel", "MySQL", "Blade", "TailwindCSS"],
      responsibilities: [
        "Full-stack development từ database đến UI",
        "Implement authentication và authorization",
        "Xây dựng cart system và checkout flow",
      ],
      learned: "Hiểu rõ kiến trúc MVC, session management và payment integration concepts.",
    },
  ];

  return (
    <section id="projects" className="py-16 sm:py-20 md:py-24">
      <div className="section-container">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6 }}
          className="text-center mb-10 sm:mb-16"
        >
          <h2 className="section-title">
            Dự án tiêu biểu / <span className="text-gradient">Featured Projects</span>
          </h2>
          <p className="section-subtitle px-2">
            Các dự án thực tế tôi đã tham gia phát triển
          </p>
        </motion.div>

        <div className="space-y-4 sm:space-y-6 md:space-y-8">
          {projects.map((project, index) => (
            <motion.article
              key={project.title}
              initial={{ opacity: 0, y: 40 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.6, delay: index * 0.1 }}
              className="group p-4 sm:p-6 md:p-8 rounded-xl sm:rounded-2xl bg-card border border-border hover:border-primary/30 transition-all duration-300"
            >
              {/* Mobile: Stack layout */}
              <div className="flex flex-col gap-4 sm:gap-6">
                {/* Header */}
                <div>
                  <div className="flex items-start gap-3 sm:gap-4 mb-3 sm:mb-4">
                    <div className="w-10 h-10 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                      <project.icon className="w-5 h-5 sm:w-6 sm:h-6 text-primary" />
                    </div>
                    <div className="min-w-0 flex-1">
                      <h3 className="text-base sm:text-lg md:text-xl font-semibold text-foreground leading-tight">{project.title}</h3>
                      <p className="text-xs sm:text-sm text-muted-foreground mt-0.5">{project.subtitle}</p>
                    </div>
                  </div>
                  
                  <div className="flex flex-wrap items-center gap-2 mb-3 sm:mb-4">
                    <span className="inline-block px-2 sm:px-3 py-0.5 sm:py-1 rounded-full bg-secondary text-[10px] sm:text-xs font-medium text-secondary-foreground">
                      {project.complexity}
                    </span>
                  </div>
                  
                  {/* Tech stack */}
                  <div className="flex flex-wrap gap-1.5 sm:gap-2">
                    {project.techStack.map((tech) => (
                      <span
                        key={tech}
                        className="px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-md bg-muted text-[10px] sm:text-xs text-muted-foreground"
                      >
                        {tech}
                      </span>
                    ))}
                  </div>
                </div>

                {/* Details */}
                <div className="space-y-3 sm:space-y-4">
                  <div>
                    <h4 className="text-xs sm:text-sm font-semibold text-primary mb-1">Vấn đề / Problem</h4>
                    <p className="text-xs sm:text-sm text-foreground/80 leading-relaxed">{project.problem}</p>
                  </div>
                  
                  <div>
                    <h4 className="text-xs sm:text-sm font-semibold text-primary mb-1">Giải pháp / Solution</h4>
                    <p className="text-xs sm:text-sm text-foreground/80 leading-relaxed">{project.solution}</p>
                  </div>

                  <div>
                    <h4 className="text-xs sm:text-sm font-semibold text-primary mb-1.5 sm:mb-2">Trách nhiệm / Responsibilities</h4>
                    <ul className="space-y-1 sm:space-y-1.5">
                      {project.responsibilities.map((item, i) => (
                        <li key={i} className="flex items-start gap-2 text-xs sm:text-sm text-foreground/80">
                          <span className="w-1 h-1 sm:w-1.5 sm:h-1.5 rounded-full bg-primary mt-1.5 sm:mt-2 flex-shrink-0" />
                          <span>{item}</span>
                        </li>
                      ))}
                    </ul>
                  </div>

                  <div className="pt-2 sm:pt-3 border-t border-border">
                    <h4 className="text-xs sm:text-sm font-semibold text-primary mb-1">Bài học / What I Learned</h4>
                    <p className="text-xs sm:text-sm text-muted-foreground italic leading-relaxed">{project.learned}</p>
                  </div>
                </div>
              </div>
            </motion.article>
          ))}
        </div>
      </div>
    </section>
  );
};

export default ProjectsSection;
