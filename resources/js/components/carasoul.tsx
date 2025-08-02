import React from 'react';
import Slider from 'react-slick';
import 'slick-carousel/slick/slick.css';
import 'slick-carousel/slick/slick-theme.css';
import {Book} from '@/types';

type BookCarouselProps = {
  books: Book[];
};

const BookCarousel: React.FC<BookCarouselProps> = ({ books }) => {
  const settings = {
    dots: true,
    infinite: true,
    speed: 500,
    slidesToShow: 3,
    slidesToScroll: 1,
    autoplay: true,
    autoplaySpeed: 3000,
    responsive: [
      { breakpoint: 1024, settings: { slidesToShow: 2 } },
      { breakpoint: 640, settings: { slidesToShow: 1 } },
    ],
  };

  return (
    <div className="w-full px-4 py-6">
      <h2 className="text-2xl font-semibold mb-4 text-center">Featured Books</h2>
      <Slider {...settings}>
        {books.map((book, index) => (
          <div key={index} className="px-2">
            <div className="bg-white shadow rounded-xl p-4 text-center">
              <img
                src={book.cover_image_url || '/placeholder-book.jpg'}
                alt={book.title}
                className="w-full h-64 object-cover rounded-md mb-3"
              />
              <h3 className="text-lg font-medium">{book.title}</h3>
              <p className="text-sm text-gray-600">{book.author}</p>
              <button className="mt-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-4 py-2 rounded">
                Shop Now
              </button>
            </div>
          </div>
        ))}
      </Slider>
    </div>
  );
};

export default BookCarousel;