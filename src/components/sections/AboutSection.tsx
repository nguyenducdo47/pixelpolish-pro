import { motion } from "framer-motion";
import { User, Zap, BookOpen } from "lucide-react";

const AboutSection = () => {
  const highlights = [
    {
      icon: User,
      title: "Trách nhiệm",
      description: "Cam kết hoàn thành công việc với chất lượng cao nhất",
    },
    {
      icon: Zap,
      title: "Học nhanh",
      description: "Khả năng tiếp thu công nghệ mới và áp dụng vào dự án thực tế",
    },
    {
      icon: BookOpen,
      title: "Tư duy hệ thống",
      description: "Hiểu rõ luồng dữ liệu và thiết kế kiến trúc rõ ràng",
    },
  ];

  return (
    <section id="about" className="py-16 sm:py-20 md:py-24 relative">
      <div className="section-container">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6 }}
          className="text-center mb-10 sm:mb-16"
        >
          <h2 className="section-title">
            Về tôi / <span className="text-gradient">About Me</span>
          </h2>
          <p className="section-subtitle max-w-3xl mx-auto px-2">
            Junior Fullstack Developer với nền tảng vững chắc về backend development
          </p>
        </motion.div>

        <div className="grid lg:grid-cols-2 gap-8 lg:gap-12 items-center">
          <motion.div
            initial={{ opacity: 0, x: -30 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6 }}
            className="order-2 lg:order-1"
          >
            <p className="text-base sm:text-lg text-foreground/90 leading-relaxed mb-4 sm:mb-6">
              Xin chào! Tôi là một Junior Fullstack Developer vừa tốt nghiệp với khoảng 1 năm 
              kinh nghiệm làm việc thực tế. Tôi tập trung phát triển chuyên môn về 
              <span className="text-primary font-medium"> PHP Laravel</span>, với đam mê 
              thiết kế cơ sở dữ liệu và xây dựng API hiệu quả.
            </p>
            <p className="text-base sm:text-lg text-foreground/90 leading-relaxed mb-4 sm:mb-6">
              Trong thời gian ngắn, tôi đã có cơ hội tham gia các dự án thực tế từ 
              e-commerce đến hệ thống quản lý trường học và quản lý kho in ấn. Mỗi dự án 
              giúp tôi hiểu sâu hơn về cách thiết kế hệ thống có thể mở rộng.
            </p>
            <p className="text-sm sm:text-base text-muted-foreground leading-relaxed">
              Tôi tin rằng code tốt không chỉ là code chạy được, mà còn là code dễ đọc, 
              dễ bảo trì và có thể phát triển lâu dài.
            </p>
          </motion.div>

          <motion.div
            initial={{ opacity: 0, x: 30 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6, delay: 0.2 }}
            className="grid gap-3 sm:gap-4 order-1 lg:order-2"
          >
            {highlights.map((item, index) => (
              <motion.div
                key={item.title}
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ duration: 0.4, delay: 0.1 * index }}
                className="flex gap-3 sm:gap-4 p-4 sm:p-5 rounded-xl bg-card border border-border hover:border-primary/30 transition-colors"
              >
                <div className="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-primary/10 flex items-center justify-center">
                  <item.icon className="w-5 h-5 sm:w-6 sm:h-6 text-primary" />
                </div>
                <div className="min-w-0">
                  <h3 className="font-semibold text-foreground mb-0.5 sm:mb-1 text-sm sm:text-base">{item.title}</h3>
                  <p className="text-xs sm:text-sm text-muted-foreground">{item.description}</p>
                </div>
              </motion.div>
            ))}
          </motion.div>
        </div>
      </div>
    </section>
  );
};

export default AboutSection;
