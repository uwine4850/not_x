export function getCssValue(className, attributeName) {
    const element = document.getElementsByClassName(className);
    const computedStyle = getComputedStyle(element);
    return computedStyle.getPropertyValue(attributeName);
}

export function getCssValueById(id, attributeName) {
    const element = document.getElementById(id);
    const computedStyle = getComputedStyle(element);
    return computedStyle.getPropertyValue(attributeName);
}
