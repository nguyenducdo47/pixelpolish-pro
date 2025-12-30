import { motion } from "framer-motion";
import { Code, Users, TestTube, Lightbulb } from "lucide-react";

const PhilosophySection = () => {
  const principles = [
    {
      icon: Code,
      title: "Clean Code",
      description: "Code sạch, có cấu trúc rõ ràng. Naming conventions tốt, functions ngắn gọn, single responsibility.",
    },
    {
      icon: Lightbulb,
      title: "SOLID Principles",
      description: "Áp dụng nguyên tắc SOLID để code dễ mở rộng, bảo trì và test được.",
    },
    {
      icon: Users,
      title: "Code cho đồng đội",
      description: "Viết code như thể ngày mai mình phải bảo trì. Dễ đọc hơn thông minh.",
    },
    {
      icon: TestTube,
      title: "Testing mindset",
      description: "Dù chưa phải expert, nhưng luôn có ý thức viết code có thể test được.",
    },
  ];

  return (
    <section id="philosophy" className="py-24 bg-card/50">
      <div className="section-container">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6 }}
          className="text-center mb-16"
        >
          <h2 className="section-title">
            Triết lý coding / <span className="text-gradient">My Philosophy</span>
          </h2>
          <p className="section-subtitle max-w-2xl mx-auto">
            Những nguyên tắc tôi luôn tuân theo khi viết code
          </p>
        </motion.div>

        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6 }}
          className="max-w-3xl mx-auto mb-12 p-6 rounded-2xl bg-card border border-border"
        >
          <p className="text-lg text-foreground/90 leading-relaxed text-center">
            "Tôi tin rằng <span className="text-primary font-medium">code tốt</span> không chỉ 
            là code chạy được. Đó là code mà đồng đội có thể đọc hiểu, code có thể dễ dàng 
            thay đổi khi requirements thay đổi, và code không gây surprise cho người đến sau."
          </p>
        </motion.div>

        <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
          {principles.map((principle, index) => (
            <motion.div
              key={principle.title}
              initial={{ opacity: 0, y: 30 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.5, delay: index * 0.1 }}
              className="p-6 rounded-xl bg-card border border-border hover:border-primary/30 transition-all duration-300 text-center group"
            >
              <div className="w-14 h-14 rounded-xl bg-primary/10 flex items-center justify-center mx-auto mb-4 group-hover:bg-primary/20 transition-colors">
                <principle.icon className="w-7 h-7 text-primary" />
              </div>
              <h3 className="font-semibold text-foreground mb-2">{principle.title}</h3>
              <p className="text-sm text-muted-foreground">{principle.description}</p>
            </motion.div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default PhilosophySection;
