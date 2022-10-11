//import {eventListenerOptionsSupported} from './utils';
export const eventListenerOptionsSupported = () => {
  let supported = false;

  try {
    const opts = Object.defineProperty({}, 'passive', {
      get() {
        supported = true;
      }
    });

    window.addEventListener("test", null, opts);
  } catch (e) {}

  return supported;
}
const defaultOptions = {
  passive: true,
  capture: false
};

const overwriteAddEvent = (superMethod) => {
  EventTarget.prototype.addEventListener = function(type, listener, options) {
    const usesListenerOptions = typeof options === 'object';
    const useCapture = usesListenerOptions ? options.capture : options;

    options = usesListenerOptions ? options : {};
    options.passive = options.passive !== undefined ? options.passive : defaultOptions.passive;
    options.capture = useCapture !== undefined ? useCapture : defaultOptions.capture;
    
    superMethod.call(this, type, listener, options);
  };
};

const supportsPassive = eventListenerOptionsSupported();

if (supportsPassive) {
  const addEvent = EventTarget.prototype.addEventListener;
  overwriteAddEvent(addEvent);
}
