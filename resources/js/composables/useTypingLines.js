import { onUnmounted, ref, watch } from 'vue';

export function useTypingLines(source, speed = 50) {
    const displayed = ref(['', '', '']);
    const currentLine = ref(0);
    const done = ref(false);
    let timer = 0;

    function stop() {
        clearTimeout(timer);
    }

    function start() {
        stop();
        const lines = source().map((line) => String(line || ''));
        displayed.value = lines.map(() => '');
        currentLine.value = 0;
        done.value = false;

        let lineIndex = 0;
        let charIndex = 0;

        const tick = () => {
            if (lineIndex >= lines.length) {
                done.value = true;
                currentLine.value = -1;
                return;
            }

            const text = lines[lineIndex];

            if (!text) {
                lineIndex += 1;
                currentLine.value = lineIndex;
                timer = setTimeout(tick, 60);
                return;
            }

            if (charIndex < text.length) {
                const next = [...displayed.value];
                next[lineIndex] = text.slice(0, charIndex + 1);
                displayed.value = next;
                charIndex += 1;
                timer = setTimeout(tick, speed);
                return;
            }

            lineIndex += 1;
            charIndex = 0;
            currentLine.value = lineIndex;
            timer = setTimeout(tick, 260);
        };

        timer = setTimeout(tick, 200);
    }

    watch(() => source().join('\0'), start, { immediate: true });
    onUnmounted(stop);

    return { displayed, currentLine, done };
}
