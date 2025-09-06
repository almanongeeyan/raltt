import random


def generate_tile_description(name, price, designs, classification, finish, size, max_words=50):
    """
    Generate a varied tile description using random AI-like phrasing.
    Args:
        name (str): Tile name
        price (str|float): Tile price
        designs (list[str]): List of tile designs
        classification (str): Tile classification
        finish (str): Tile finish
        size (str): Tile size
        max_words (int): Maximum number of words in the description
    Returns:
        str: Generated description
    """
    designs_str = ', '.join(designs)
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
        f"Add a statement piece to your home with the unique '{name}' tile.",
        f"Experience the artistry of the '{name}' tile, a blend of tradition and innovation.",
        f"The '{name}' tile brings a new dimension to your living space.",
        f"Discover the beauty of the '{name}' tile, perfect for any project.",
        f"The '{name}' tile is a testament to quality and design.",
        f"Let the '{name}' tile inspire your next renovation.",
        f"The '{name}' tile is a favorite among designers for its versatility.",
        f"Choose the '{name}' tile for a timeless look.",
        f"The '{name}' tile is crafted for those who value excellence.",
        f"Make a bold statement with the '{name}' tile.",
        f"The '{name}' tile is the perfect finishing touch for any room."
    ]
    design_phrases = [
        f"This tile features a distinctive {designs_str} design that effortlessly complements a variety of decor styles, from modern to classic.",
        f"Its {designs_str} motif adds a unique flair, making it a focal point in any room.",
        f"With a carefully crafted {designs_str} pattern, this tile brings a sense of artistry and personality to your floors or walls.",
        f"The {designs_str} style is both eye-catching and versatile, suitable for a range of applications.",
        f"Inspired by {designs_str} elements, this tile is perfect for those who appreciate subtle yet impactful design choices.",
        f"The {designs_str} finish provides a sophisticated look that enhances any setting.",
        f"A beautiful {designs_str} surface ensures this tile stands out in any installation.",
        f"Enjoy the timeless appeal of the {designs_str} design, which never goes out of style.",
        f"The {designs_str} details are meticulously rendered for a premium appearance.",
        f"A {designs_str} accent brings warmth and character to your living space.",
        f"The {designs_str} design is a hallmark of elegance.",
        f"Let the {designs_str} pattern transform your home.",
        f"The {designs_str} look is both modern and classic.",
        f"Choose {designs_str} for a unique touch.",
        f"The {designs_str} style is sure to impress.",
        f"A {designs_str} design adds personality to your space.",
        f"The {designs_str} motif is a conversation starter.",
        f"Enjoy the sophistication of the {designs_str} design.",
        f"The {designs_str} pattern is perfect for any decor.",
        f"The {designs_str} surface is easy to maintain and beautiful to behold."
    ]
    classification_phrases = [
        f"It is classified as a {classification} tile, offering unique benefits for specific applications.",
        f"This tile belongs to the {classification} category, ensuring suitability for your needs.",
        f"A {classification} tile, it is designed for optimal performance.",
        f"The {classification} classification guarantees quality and reliability.",
        f"Choose a {classification} tile for peace of mind.",
        f"The {classification} type is ideal for a range of projects.",
        f"With its {classification} classification, this tile stands out.",
        f"A trusted {classification} tile for any environment.",
        f"The {classification} label means this tile meets high standards.",
        f"Opt for a {classification} tile for lasting value."
    ]
    finish_phrases = [
        f"The finish of this tile is {finish}, providing a distinct look and feel.",
        f"Enjoy the {finish} finish, which adds character to your space.",
        f"A {finish} finish ensures this tile is both stylish and practical.",
        f"The {finish} surface is easy to clean and maintain.",
        f"Choose a {finish} finish for a modern touch.",
        f"The {finish} look is timeless and elegant.",
        f"A {finish} finish complements any decor.",
        f"The {finish} surface is durable and attractive.",
        f"Enjoy the beauty of a {finish} finish.",
        f"The {finish} finish is a favorite among homeowners."
    ]
    size_phrases = [
        f"Each tile measures {size}, making it suitable for a variety of layouts and room sizes.",
        f"The {size} size is perfect for both small and large spaces.",
        f"A {size} tile offers flexibility in design.",
        f"Choose the {size} size for a seamless look.",
        f"The {size} dimension is ideal for creative patterns.",
        f"A {size} tile is easy to install and maintain.",
        f"The {size} size is a popular choice for renovations.",
        f"Enjoy the versatility of the {size} tile.",
        f"The {size} measurement fits any project.",
        f"A {size} tile is perfect for custom designs."
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
        f"A smart investment at ₱{price} per tile, offering both style and savings.",
        f"The price of ₱{price} makes this tile a great deal.",
        f"Get premium quality for only ₱{price} per tile.",
        f"At ₱{price}, this tile is a budget-friendly option.",
        f"The ₱{price} price point is hard to beat.",
        f"Enjoy designer looks for just ₱{price}.",
        f"This tile is a steal at ₱{price} per piece.",
        f"For ₱{price}, you get both style and value.",
        f"A luxury tile at a ₱{price} price.",
        f"The ₱{price} cost makes this tile accessible to all.",
        f"Upgrade your space for only ₱{price} per tile."
    ]
    extras = [
        "Whether you're renovating a bathroom, kitchen, or entryway, this tile is sure to impress guests and residents alike.",
        "Its easy-to-clean surface and robust construction make it a practical choice for busy households.",
        f"Choose the '{name}' tile for a blend of elegance, reliability, and long-lasting performance.",
        "Let this tile be the foundation of your next design project, bringing your vision to life.",
        "Order now and experience the difference that quality tiles can make in your space.",
        "This tile is engineered for everyday living, combining style with durability.",
        "Enjoy peace of mind with a tile that's built to last and easy to maintain.",
        "A favorite among interior designers for its versatility and appeal.",
        "Make a lasting impression with a tile that stands out for all the right reasons.",
        "Bring your creative vision to life with a tile that adapts to your needs."
    ]
    desc_parts = [
        random.choice(intros),
        random.choice(design_phrases),
        random.choice(classification_phrases),
        random.choice(finish_phrases),
        random.choice(size_phrases),
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
    parser.add_argument('--designs', type=str, required=True, help='Base64-encoded JSON list of designs')
    parser.add_argument('--classification', type=str, required=True)
    parser.add_argument('--finish', type=str, required=True)
    parser.add_argument('--size', type=str, required=True)
    parser.add_argument('--max_words', type=int, default=50)
    args = parser.parse_args()
    try:
        designs_json = base64.b64decode(args.designs).decode('utf-8')
        designs = json.loads(designs_json)
        if not isinstance(designs, list):
            designs = []
    except Exception:
        designs = []
    desc = generate_tile_description(
        args.name,
        args.price,
        designs,
        args.classification,
        args.finish,
        args.size,
        args.max_words
    )
    print(desc)
