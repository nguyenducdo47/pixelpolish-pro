import { motion } from "framer-motion";

const TechStackSection = () => {
  const categories = [
    {
      title: "Backend",
      items: [
        {
          name: "PHP 8",
          description: "OOP, typed properties, attributes, performance tuning",
        },
        {
          name: "Laravel",
          description: "Eloquent ORM, Queues, Events, API Resources, Testing",
        },
      ],
    },
    {
      title: "Database",
      items: [
        {
          name: "MySQL",
          description: "Query optimization, indexing, transaction management",
        },
        {
          name: "Redis",
          description: "Caching, session management, queue driver",
        },
      ],
    },
    {
      title: "Frontend",
      items: [
        {
          name: "Blade Template",
          description: "Laravel templating, components, layouts",
        },
        {
          name: "TailwindCSS",
          description: "Utility-first CSS, responsive design",
        },
        {
          name: "Vue.js (Basic)",
          description: "Component-based UI, reactivity basics",
        },
      ],
    },
    {
      title: "Tools & DevOps",
      items: [
        {
          name: "Git",
          description: "Version control, branching strategies, collaboration",
        },
        {
          name: "Docker",
          description: "Containerization basics, local development",
        },
        {
          name: "AWS (Basic)",
          description: "EC2 deployment, S3 storage awareness",
        },
      ],
    },
  ];

  return (
    <section id="skills" className="py-16 sm:py-20 md:py-24 bg-card/50">
      <div className="section-container">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6 }}
          className="text-center mb-10 sm:mb-16"
        >
          <h2 className="section-title">
            Công nghệ sử dụng / <span className="text-gradient">Tech Stack</span>
          </h2>
          <p className="section-subtitle px-2">
            Các công nghệ tôi đã làm việc trong các dự án thực tế
          </p>
        </motion.div>

        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
          {categories.map((category, categoryIndex) => (
            <motion.div
              key={category.title}
              initial={{ opacity: 0, y: 30 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.5, delay: categoryIndex * 0.1 }}
              className="p-4 sm:p-6 rounded-xl sm:rounded-2xl bg-card border border-border hover:border-primary/30 transition-all duration-300"
            >
              <h3 className="text-lg sm:text-xl font-semibold text-primary mb-3 sm:mb-5">{category.title}</h3>
              <div className="space-y-3 sm:space-y-4">
                {category.items.map((item, itemIndex) => (
                  <motion.div
                    key={item.name}
                    initial={{ opacity: 0, x: -10 }}
                    whileInView={{ opacity: 1, x: 0 }}
                    viewport={{ once: true }}
                    transition={{ duration: 0.3, delay: (categoryIndex * 0.1) + (itemIndex * 0.05) }}
                    className="group"
                  >
                    <div className="flex items-start gap-2 sm:gap-3">
                      <div className="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-primary mt-1.5 sm:mt-2 flex-shrink-0 group-hover:scale-125 transition-transform" />
                      <div className="min-w-0">
                        <h4 className="font-medium text-foreground text-sm sm:text-base">{item.name}</h4>
                        <p className="text-xs sm:text-sm text-muted-foreground break-words">{item.description}</p>
                      </div>
                    </div>
                  </motion.div>
                ))}
              </div>
            </motion.div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default TechStackSection;
