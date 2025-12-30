import { useState, useEffect } from "react";

interface UseTypingEffectOptions {
  speed?: number;
  delay?: number;
}

export const useTypingEffect = (
  text: string,
  options: UseTypingEffectOptions = {}
) => {
  const { speed = 50, delay = 0 } = options;
  const [displayedText, setDisplayedText] = useState("");
  const [isComplete, setIsComplete] = useState(false);

  useEffect(() => {
    let timeout: NodeJS.Timeout;
    let currentIndex = 0;

    const startTyping = () => {
      if (currentIndex < text.length) {
        setDisplayedText(text.slice(0, currentIndex + 1));
        currentIndex++;
        timeout = setTimeout(startTyping, speed);
      } else {
        setIsComplete(true);
      }
    };

    const delayTimeout = setTimeout(startTyping, delay);

    return () => {
      clearTimeout(delayTimeout);
      clearTimeout(timeout);
    };
  }, [text, speed, delay]);

  return { displayedText, isComplete };
};
