import {
   store,
   getElement,
   useState,
   useEffect,
} from "@wordpress/interactivity"

const getVisibility = () => {
   const [inView, setInView] = useState(false)

   useEffect(() => {
      const { ref } = getElement()
      const threshold = window.innerWidth < 768 ? 0.25 : 1
      const rootMargin = window.innerHeight < 1000 ? "100px" : "0px"

      const observer = new IntersectionObserver(
         ([entry]) => {
            setInView(entry.isIntersecting)
         },
         { threshold, rootMargin }
      )
      observer.observe(ref)
      return () => ref && observer.unobserve(ref)
   }, [])
   return inView
}

const { state } = store("gutenberg-store", {
   callbacks: {
      setActive: () => {
         const { ref } = getElement()
         ref.classList.add("animate")
      },

      intersect: () => {
         const ref = getElement()
         const isVisibleObserver = getVisibility()

         useEffect(() => {
            if (isVisibleObserver) {
               ref.ref.classList.add("intersecting")
            }
         })
      },

      intersectFooter: () => {
         const isVisibleObserver = getVisibility()

         useEffect(() => {
            if (isVisibleObserver) {
               document
                  .querySelector(".footer-stroke")
                  .classList.add("intersecting")
            }
         })
      },
   },
})
