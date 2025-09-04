import random

def generate_tile_description(name, price, design, stock, categories, max_words=50):
    """
    Generate a varied tile description using random AI-like phrasing.
    Args:
        name (str): Tile name
        price (str|float): Tile price
        design (str): Tile design
        stock (str|int): Stock count
        categories (list[str]): List of category names
        max_words (int): Maximum number of words in the description
    Returns:
        str: Generated description
    """
    intros = [
        f"The '{name}' tile is a wonderful choice for anyone looking to add a touch of sophistication and charm to their space.",
        f"Elevate your interiors with the '{name}' tile, designed to bring both beauty and durability to your home.",
        f"If you're searching for a tile that combines style and practicality, the '{name}' is an excellent option.",
        f"Redefine your living or working area with the timeless appeal of the '{name}' tile.",
        f"The '{name}' tile stands out for its exceptional craftsmanship and attention to detail.",
        f"Bring a fresh perspective to your space with the innovative '{name}' tile.",
        f"Step into a world of style with the remarkable '{name}' tile, crafted for discerning tastes.",
        f"Transform your environment with the versatile and attractive '{name}' tile.",
        f"The '{name}' tile is designed to meet the needs of modern living while maintaining classic elegance.",
        f"Add a statement piece to your home with the unique '{name}' tile."
    ]
    design_phrases = [
        f"This tile features a distinctive {design} design that effortlessly complements a variety of decor styles, from modern to classic.",
        f"Its {design} motif adds a unique flair, making it a focal point in any room.",
        f"With a carefully crafted {design} pattern, this tile brings a sense of artistry and personality to your floors or walls.",
        f"The {design} style is both eye-catching and versatile, suitable for a range of applications.",
        f"Inspired by {design} elements, this tile is perfect for those who appreciate subtle yet impactful design choices.",
        f"The {design} finish provides a sophisticated look that enhances any setting.",
        f"A beautiful {design} surface ensures this tile stands out in any installation.",
        f"Enjoy the timeless appeal of the {design} design, which never goes out of style.",
        f"The {design} details are meticulously rendered for a premium appearance.",
        f"A {design} accent brings warmth and character to your living space."
    ]
    category_phrases = [
        f"It is especially ideal for use in {', '.join(categories)} settings, where its qualities can truly shine." if categories else "It is suitable for installation in any room, offering flexibility for your design needs.",
        f"Perfect for {', '.join(categories)} applications, this tile adapts seamlessly to your requirements." if categories else "A versatile choice, it fits well in both residential and commercial spaces.",
        f"Blending harmoniously with {', '.join(categories)} themes, it enhances the overall ambiance of your environment." if categories else "Its neutral appeal makes it easy to coordinate with existing decor.",
        f"A great addition to {', '.join(categories)} areas, this tile is both functional and stylish." if categories else "Enhance any space with its understated elegance.",
        f"Recommended for {', '.join(categories)} projects, this tile delivers both form and function." if categories else "This tile is a smart solution for any renovation or new build.",
        f"Whether for {', '.join(categories)}, or beyond, this tile is sure to meet your expectations." if categories else "Its adaptable nature makes it suitable for a wide range of uses.",
        f"The perfect finishing touch for {', '.join(categories)} environments." if categories else "A reliable choice for any interior design scheme.",
        f"Designed for {', '.join(categories)} but versatile enough for any space." if categories else "A timeless option for all types of rooms.",
        f"A favorite among designers for {', '.join(categories)} installations." if categories else "Loved by professionals and homeowners alike."
    ]
    price_phrases = [
        f"Available for only ₱{price} per piece, it offers outstanding value for its quality.",
        f"Priced at just ₱{price}, this tile is an affordable way to upgrade your space.",
        f"Enjoy the luxury look and feel at a reasonable price of ₱{price} per tile.",
        f"With a price tag of ₱{price}, it is accessible without compromising on style.",
        f"Exceptional value at ₱{price} per piece, making premium design accessible.",
        f"A cost-effective solution at ₱{price} per tile, perfect for any budget.",
        f"Invest in quality without overspending—just ₱{price} per tile.",
        f"Upgrade your home for less with this tile at ₱{price} each.",
        f"Affordable elegance is yours for ₱{price} per piece.",
        f"A smart investment at ₱{price} per tile, offering both style and savings."
    ]
    # Removed stock_phrases
    extras = [
        "Whether you're renovating a bathroom, kitchen, or entryway, this tile is sure to impress guests and residents alike.",
        "Its easy-to-clean surface and robust construction make it a practical choice for busy households.",
        "Choose the '{name}' tile for a blend of elegance, reliability, and long-lasting performance.",
        "Let this tile be the foundation of your next design project, bringing your vision to life.",
        "Order now and experience the difference that quality tiles can make in your space.",
        "This tile is engineered for everyday living, combining style with durability.",
        "Enjoy peace of mind with a tile that's built to last and easy to maintain.",
        "A favorite among interior designers for its versatility and appeal.",
        "Make a lasting impression with a tile that stands out for all the right reasons.",
        "Bring your creative vision to life with a tile that adapts to your needs."
    ]
    # Randomly select one from each
    desc_parts = [
        random.choice(intros),
        random.choice(design_phrases),
        random.choice(category_phrases),
        random.choice(price_phrases),
        random.choice(extras)
    ]
    desc = ' '.join(desc_parts)
    # Limit to max_words, but do not cut sentences
    if max_words is not None:
        sentences = desc.split('. ')
        result = []
        word_count = 0
        for s in sentences:
            s = s.strip()
            if not s:
                continue
            words_in_s = len(s.split())
            if word_count + words_in_s > max_words:
                break
            result.append(s)
            word_count += words_in_s
        desc = '. '.join(result)
        if desc and not desc.endswith('.'):
            desc += '.'
    return desc

# CLI usage for PHP integration
import argparse
import json

if __name__ == "__main__":
    import base64
    parser = argparse.ArgumentParser()
    parser.add_argument('--name', type=str, required=True)
    parser.add_argument('--price', type=str, required=True)
    parser.add_argument('--design', type=str, required=True)
    parser.add_argument('--stock', type=str, required=True)
    parser.add_argument('--categories', type=str, required=True, help='Base64-encoded JSON list of categories')
    parser.add_argument('--max_words', type=int, default=50)
    args = parser.parse_args()
    try:
        categories_json = base64.b64decode(args.categories).decode('utf-8')
        categories = json.loads(categories_json)
        if not isinstance(categories, list):
            categories = []
    except Exception:
        categories = []
    desc = generate_tile_description(args.name, args.price, args.design, args.stock, categories, args.max_words)
    print(desc)
