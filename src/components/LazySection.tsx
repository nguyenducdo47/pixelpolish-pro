import { ReactNode } from "react";
import { useLazySection } from "@/hooks/useLazySection";
import { cn } from "@/lib/utils";

interface LazySectionProps {
  children: ReactNode;
  className?: string;
}

const LazySection = ({ children, className }: LazySectionProps) => {
  const { ref, isVisible } = useLazySection({ rootMargin: "50px" });

  return (
    <div
      ref={ref}
      className={cn(
        "transition-all duration-700 ease-out",
        isVisible 
          ? "opacity-100 translate-y-0" 
          : "opacity-0 translate-y-8",
        className
      )}
    >
      {isVisible ? children : <div className="min-h-[200px]" />}
    </div>
  );
};

export default LazySection;
