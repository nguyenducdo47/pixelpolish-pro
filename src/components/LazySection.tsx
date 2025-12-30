import { ReactNode, useRef } from "react";
import { motion, useScroll, useTransform } from "framer-motion";
import { useLazySection } from "@/hooks/useLazySection";
import { cn } from "@/lib/utils";

interface LazySectionProps {
  children: ReactNode;
  className?: string;
  parallaxSpeed?: number;
}

const LazySection = ({ children, className, parallaxSpeed = 0.15 }: LazySectionProps) => {
  const { ref, isVisible } = useLazySection({ rootMargin: "200px" });
  const containerRef = useRef<HTMLDivElement>(null);

  const { scrollYProgress } = useScroll({
    target: containerRef,
    offset: ["start end", "end start"]
  });

  const y = useTransform(scrollYProgress, [0, 1], [60 * parallaxSpeed, -60 * parallaxSpeed]);

  return (
    <div ref={containerRef} className={cn("overflow-hidden", className)}>
      <div ref={ref}>
        <motion.div
          style={{ y }}
          initial={{ opacity: 0 }}
          animate={{ opacity: isVisible ? 1 : 0 }}
          transition={{ duration: 0.7, ease: "easeOut" }}
        >
          {children}
        </motion.div>
      </div>
    </div>
  );
};

export default LazySection;
