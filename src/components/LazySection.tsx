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
  const { ref, isVisible } = useLazySection({ rootMargin: "50px" });
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
          style={{ y: isVisible ? y : 0 }}
          className={cn(
            "transition-opacity duration-700 ease-out",
            isVisible ? "opacity-100" : "opacity-0"
          )}
        >
          {isVisible ? children : <div className="min-h-[200px]" />}
        </motion.div>
      </div>
    </div>
  );
};

export default LazySection;
