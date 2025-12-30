import { motion } from "framer-motion";
import { ExternalLink, Layers, Shield, ShoppingCart } from "lucide-react";
import { Button } from "@/components/ui/button";

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
    <section id="projects" className="py-24">
      <div className="section-container">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6 }}
          className="text-center mb-16"
        >
          <h2 className="section-title">
            Dự án tiêu biểu / <span className="text-gradient">Featured Projects</span>
          </h2>
          <p className="section-subtitle">
            Các dự án thực tế tôi đã tham gia phát triển
          </p>
        </motion.div>

        <div className="space-y-8">
          {projects.map((project, index) => (
            <motion.article
              key={project.title}
              initial={{ opacity: 0, y: 40 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.6, delay: index * 0.1 }}
              className="group p-6 sm:p-8 rounded-2xl bg-card border border-border hover:border-primary/30 transition-all duration-300"
            >
              <div className="flex flex-col lg:flex-row gap-6">
                {/* Left side - Header */}
                <div className="lg:w-1/3">
                  <div className="flex items-start gap-4 mb-4">
                    <div className="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                      <project.icon className="w-6 h-6 text-primary" />
                    </div>
                    <div>
                      <h3 className="text-xl font-semibold text-foreground">{project.title}</h3>
                      <p className="text-sm text-muted-foreground">{project.subtitle}</p>
                    </div>
                  </div>
                  <span className="inline-block px-3 py-1 rounded-full bg-secondary text-xs font-medium text-secondary-foreground">
                    {project.complexity}
                  </span>
                  
                  {/* Tech stack */}
                  <div className="flex flex-wrap gap-2 mt-4">
                    {project.techStack.map((tech) => (
                      <span
                        key={tech}
                        className="px-2 py-1 rounded-md bg-muted text-xs text-muted-foreground"
                      >
                        {tech}
                      </span>
                    ))}
                  </div>
                </div>

                {/* Right side - Details */}
                <div className="lg:w-2/3 space-y-4">
                  <div>
                    <h4 className="text-sm font-semibold text-primary mb-1">Vấn đề / Problem</h4>
                    <p className="text-sm text-foreground/80">{project.problem}</p>
                  </div>
                  
                  <div>
                    <h4 className="text-sm font-semibold text-primary mb-1">Giải pháp / Solution</h4>
                    <p className="text-sm text-foreground/80">{project.solution}</p>
                  </div>

                  <div>
                    <h4 className="text-sm font-semibold text-primary mb-2">Trách nhiệm / Responsibilities</h4>
                    <ul className="space-y-1">
                      {project.responsibilities.map((item, i) => (
                        <li key={i} className="flex items-start gap-2 text-sm text-foreground/80">
                          <span className="w-1.5 h-1.5 rounded-full bg-primary mt-2 flex-shrink-0" />
                          {item}
                        </li>
                      ))}
                    </ul>
                  </div>

                  <div className="pt-2 border-t border-border">
                    <h4 className="text-sm font-semibold text-primary mb-1">Bài học / What I Learned</h4>
                    <p className="text-sm text-muted-foreground italic">{project.learned}</p>
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
