const testimonials = [
  {
    quote: "Integral Safety provides our Trustees with the assurance we are complying with the latest H&S requirements. Excellent value for money — we would highly recommend them.",
    authorName: "Chris R Jones",
    authorTitle: "CEO",
    companyName: "Wyggestons Hospital Trust",
    initials: "CJ",
    rating: 5,
  },
  {
    quote: "Impressed with the clarity and detailed schedule of images from the drone survey. Quick turnaround and excellent client care. Highly recommend.",
    authorName: "Scott Keegan",
    authorTitle: "Managing Director",
    companyName: "KestrelQAS Ltd",
    initials: "SK",
    rating: 5,
  },
]

export function Testimonials() {
  return (
    <section className="py-24 bg-cream">
      <div className="container">
        {/* Header */}
        <div className="text-center mb-14">
          <p className="section-eyebrow">Testimonials</p>
          <h2 className="section-title">What Our Clients Say</h2>
        </div>

        {/* Grid */}
        <div className="grid md:grid-cols-2 gap-8">
          {testimonials.map((testimonial) => (
            <div
              key={testimonial.authorName}
              className="bg-white rounded-card p-8 relative"
            >
              {/* Quote Mark */}
              <div className="absolute top-4 right-8 font-heading text-[6rem] text-orange-100 leading-none select-none">
                &ldquo;
              </div>

              {/* Stars */}
              <div className="text-orange-500 text-lg tracking-wider mb-4">
                {'★'.repeat(testimonial.rating)}
              </div>

              {/* Quote */}
              <p className="text-navy-800 text-[1.05rem] leading-relaxed mb-6 relative z-10">
                &ldquo;{testimonial.quote}&rdquo;
              </p>

              {/* Author */}
              <div className="flex items-center gap-3">
                <div className="w-12 h-12 bg-navy-700 rounded-full flex items-center justify-center text-white font-semibold">
                  {testimonial.initials}
                </div>
                <div>
                  <strong className="block text-navy-900">{testimonial.authorName}</strong>
                  <span className="text-sm text-gray-600">
                    {testimonial.authorTitle}, {testimonial.companyName}
                  </span>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  )
}
