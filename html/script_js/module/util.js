/**
 * @param {?HTMLElement} target : element html vers lequel scroller
 */
export function scroller(target) {
    if (target) {
        target.scrollIntoView({ behavior: "instant", block: "start" });
    } else {
        console.log("Impossible de scroller");
    }
}
