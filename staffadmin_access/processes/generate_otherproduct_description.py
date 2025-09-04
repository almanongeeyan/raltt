import random

def generate_otherproduct_description(name, price, spec, max_words=50):
    """
    Generate a varied description for 'other' products (not tiles), with phrases ideal for the selected specification.
    Args:
        name (str): Product name (from text input)
        price (str|float): Product price (from text input)
        spec (str): Product specification (from dropdown)
        max_words (int): Maximum number of words in the description
    Returns:
        str: Generated description
    """
    intros = [
        f"Introducing the {name}, a top choice for those seeking quality and reliability.",
        f"Upgrade your space with the versatile {name}, designed for modern needs.",
        f"The {name} stands out for its exceptional build and thoughtful design.",
        f"Experience the difference with the {name}, crafted for everyday use.",
        f"The {name} is engineered to deliver both style and performance.",
        f"Discover the benefits of the {name}, a must-have for any project.",
        f"Enhance your home or business with the trusted {name}.",
        f"Choose the {name} for a blend of innovation and practicality.",
        f"The {name} is a smart addition to any setting, offering lasting value.",
        f"Bring efficiency and elegance together with the {name}."
    ]

    # Custom phrases for each spec
    spec_ideal_phrases = {
        "PVC Doors": [
            "Perfect for house doors, providing privacy and security.",
            "A simple and effective solution for any area that needs a door.",
            "Ideal for bathrooms, kitchens, and areas exposed to moisture.",
            "Great for renovations or new builds needing affordable, durable doors.",
            "Simply a door—versatile for any room or project.",
            "Recommended for both residential and commercial interiors.",
            "Designed to withstand humidity and frequent use.",
            "A practical choice for easy-to-clean, stylish door solutions.",
            "Suitable for anything that needs a door, from homes to offices.",
            "A budget-friendly option for replacing or adding doors."
        ],
        "Sinks": [
            "Perfect for kitchen areas, making washing plates and utensils easy.",
            "Designed for washing plates, cookware, and kitchen items.",
            "A must-have for any kitchen or utility area.",
            "Great for washing plates and all your kitchen stuff.",
            "Ideal for daily use and easy maintenance.",
            "A practical addition to any home or business.",
            "Designed for efficient water flow and durability.",
            "Recommended for renovations and new installations.",
            "Essential for food prep and cleaning tasks.",
            "A reliable choice for busy kitchens."
        ],
        "Tile Vinyl": [
            "A budget-friendly version of tiles—perfect as a tile substitute.",
            "Ideal for quick and stylish floor upgrades.",
            "Great for those seeking a cost-effective alternative to traditional tiles.",
            "Perfect for high-traffic areas needing easy-to-clean surfaces.",
            "A smart choice for both homes and offices.",
            "Designed for simple installation and long-lasting beauty.",
            "Recommended for living rooms, bedrooms, and commercial spaces.",
            "Upgrade your floors without breaking the bank.",
            "A practical solution for budget-conscious renovations.",
            "Easy to install and maintain, making it ideal for DIY projects."
        ],
        "Bowls": [
            "A type of bowl for comfort rooms, where feces is stored (toilet bowl).",
            "Essential for any comfort room or bathroom setup.",
            "Designed for sanitation and comfort in restrooms.",
            "A practical solution for modern bathrooms.",
            "Reliable and easy to clean, perfect for daily use.",
            "A must-have for any home or commercial restroom.",
            "Engineered for efficient waste management.",
            "Durable and built to last in high-use environments.",
            "A staple fixture for comfort rooms everywhere.",
            "Combines function and hygiene for your bathroom needs."
        ]
    }
    # Fallback if spec not in list
    default_spec_phrases = [
        f"This product features a {spec} specification, making it ideal for a variety of applications.",
        f"With its {spec} design, it adapts seamlessly to your requirements.",
        f"Engineered with a focus on {spec}, this product ensures dependable performance.",
        f"The {spec} aspect provides unique advantages for your needs.",
        f"A {spec} product that delivers both function and style.",
        f"Its {spec} build is perfect for those who value quality and versatility.",
        f"Enjoy the benefits of a {spec} product in your next project.",
        f"The {spec} feature sets this product apart from the rest.",
        f"A reliable {spec} solution for any environment.",
        f"The {spec} specification ensures it meets a wide range of demands."
    ]
    # Always include a phrase ideal for the selected spec
    ideal_phrase = random.choice(spec_ideal_phrases.get(spec, default_spec_phrases))

    price_phrases = [
        f"Available for only ₱{price}, it offers excellent value for its quality.",
        f"Priced at just ₱{price}, this product is an affordable upgrade.",
        f"Enjoy premium features at a reasonable price of ₱{price}.",
        f"With a price tag of ₱{price}, it is accessible without compromise.",
        f"Exceptional value at ₱{price}, making quality accessible.",
        f"A cost-effective solution at ₱{price}, perfect for any budget.",
        f"Invest in quality for just ₱{price}.",
        f"Upgrade your space for less with this product at ₱{price}.",
        f"Affordable excellence is yours for ₱{price}.",
        f"A smart investment at ₱{price}, offering both style and savings."
    ]
    extras = [
        "Perfect for renovations, new builds, or simple upgrades.",
        "Its robust construction ensures long-lasting performance.",
        "A favorite among professionals for its reliability.",
        "Easy to install and maintain, making it a practical choice.",
        "Order now and experience the benefits firsthand.",
        "Engineered for everyday use, combining style with durability.",
        "Enjoy peace of mind with a product that's built to last.",
        "A trusted choice for both residential and commercial projects.",
        "Make a lasting impression with a product that stands out.",
        "Bring your vision to life with a product that adapts to your needs."
    ]
    desc_parts = [
        random.choice(intros),
        ideal_phrase,
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

if __name__ == "__main__":
    parser = argparse.ArgumentParser()
    parser.add_argument('--name', type=str, required=True)
    parser.add_argument('--price', type=str, required=True)
    parser.add_argument('--spec', type=str, required=True)
    parser.add_argument('--max_words', type=int, default=50)
    args = parser.parse_args()
    desc = generate_otherproduct_description(args.name, args.price, args.spec, args.max_words)
    import sys
    try:
        sys.stdout.buffer.write((desc + '\n').encode('utf-8'))
    except Exception as e:
        print('ERROR:', e, file=sys.stderr)
