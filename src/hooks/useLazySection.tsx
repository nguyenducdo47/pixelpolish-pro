import { useEffect, useRef, useState } from "react";

interface UseLazySectionOptions {
  threshold?: number;
  rootMargin?: string;
}

export const useLazySection = (options: UseLazySectionOptions = {}) => {
  const { threshold = 0.1, rootMargin = "100px" } = options;
  const ref = useRef<HTMLDivElement>(null);
  const [isVisible, setIsVisible] = useState(false);

  useEffect(() => {
    const element = ref.current;
    if (!element) return;

    const observer = new IntersectionObserver(
      ([entry]) => {
        if (entry.isIntersecting) {
          setIsVisible(true);
          observer.unobserve(element);
        }
      },
      { threshold, rootMargin }
    );

    observer.observe(element);

    return () => {
      observer.unobserve(element);
    };
  }, [threshold, rootMargin]);

  return { ref, isVisible };
};
